<?php declare(strict_types=1);
/**
 * LICENSE
 *
 * This software and its source code is protected by copyright law (Sec. 69a ff. UrhG).
 * It is not allowed to make any kinds of modifications, nor must it be copied,
 * or published without explicit permission. Misuse will lead to persecution.
 *
 * @copyright  2021 infomax websolutions GmbH
 * @link       http://www.infomax-it.de
 */

namespace de\codenamephp\deployer\mariadb\task;

use de\codenamephp\deployer\base\functions\All;
use de\codenamephp\deployer\base\functions\iAll;
use de\codenamephp\deployer\base\functions\iInput;
use de\codenamephp\deployer\base\hostCheck\DoNotRunOnProduction;
use de\codenamephp\deployer\base\hostCheck\iHostCheck;
use de\codenamephp\deployer\base\hostCheck\SkippableByOption;
use de\codenamephp\deployer\base\iConfigurationKeys;
use de\codenamephp\deployer\base\MissingInputException;
use de\codenamephp\deployer\base\UnsafeOperationException;
use de\codenamephp\deployer\mariadb\database\factory\database\iDatabase;
use de\codenamephp\deployer\mariadb\database\factory\database\SimpleNew;
use de\codenamephp\deployer\mariadb\dumpfile\factory\iDumpfile;
use de\codenamephp\deployer\mariadb\task\deleteDump\factory\iDeleteDump;
use de\codenamephp\deployer\mariadb\task\download\factory\iDownload;
use de\codenamephp\deployer\mariadb\task\dump\factory\iDump;
use de\codenamephp\deployer\mariadb\task\import\factory\iImport;
use de\codenamephp\deployer\mariadb\task\upload\factory\iUpload;
use Deployer\Host\Host;

/**
 * Copies the database from one host to another. The target host is the stage and the source has to be given
 * using the db:sourceHost option. If no source is given it defaults to production.
 *
 * So dep db:copy staging --db:sourceHost=main would copy the database from staging to main while dep db:copy main would copy from production to main
 *
 * @psalm-api
 */
final class Copy implements iTask {

  public const DB_SOURCE_HOST = 'cpd:db:sourceHost';

  public function __construct(public iDumpfile   $dumpfile = new \de\codenamephp\deployer\mariadb\dumpfile\factory\fromDatabaseNameAndTimestamp\SimpleNew(),
                              public iDatabase   $database = new SimpleNew(),
                              public iDump       $dump = new dump\factory\SimpleNew(),
                              public iDeleteDump $deleteDump = new deleteDump\factory\SimpleNew(),
                              public iImport     $import = new import\factory\SimpleNew(),
                              public iDownload   $download = new download\factory\SimpleNew(),
                              public iUpload     $upload = new upload\factory\SimpleNew(),
                              public iHostCheck  $hostCheck = new SkippableByOption(new DoNotRunOnProduction()),
                              public iAll        $deployerFunctions = new All()) {
    $this->deployerFunctions->option(self::DB_SOURCE_HOST, null, iInput::OPTION_VALUE_REQUIRED, 'The source host to copy the database(s) from', iConfigurationKeys::PRODUCTION);
  }

  /**
   * Gets the source host name from the input option and tries to get the host from deployer
   *
   * @return Host|Host[]
   * @throws MissingInputException if the source host option was not set
   */
  public function findSourceHost() : Host|array {
    $hostname = (string) $this->deployerFunctions->getOption(self::DB_SOURCE_HOST);
    if($hostname === '') throw new MissingInputException('The option for the source host must not be empty');

    return $this->deployerFunctions->host($hostname);
  }

  /**
   * Creates a single dumpfile that will be used for all instances, creates a dump on the source host, downloads it to local, uploads it to target
   * and imports it there. This way there is no connection between the hosts so we don't have to configure additional access privileges.
   *
   * @throws UnsafeOperationException when the task is run on production stage
   */
  public function __invoke() : void {
    $this->hostCheck->check();

    $targetDatabase = $this->database->fromConfigKey();
    $dumpfile = $this->dumpfile->create($targetDatabase);
    $deleteDump = $this->deleteDump->create($dumpfile);

    $this->deployerFunctions->on($this->findSourceHost(), function() use ($dumpfile, $deleteDump) {
      $this->dump->create($this->database->fromConfigKey(), $dumpfile)();
      $this->download->createWithSingleDumpfile($dumpfile)();
      $deleteDump();
    });

    $this->upload->createWithSingleDumpfile($dumpfile)();
    $this->import->create($targetDatabase, $dumpfile)();
    $deleteDump();

    $this->deployerFunctions->on($this->deployerFunctions->localhost(), static fn() => $deleteDump());
  }
}
