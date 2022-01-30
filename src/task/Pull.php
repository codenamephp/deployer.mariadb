<?php declare(strict_types=1);
/**
 * LICENSE
 *
 * This software and its source code is protected by copyright law (Sec. 69a ff. UrhG).
 * It is not allowed to make any kinds of modifications, nor must it be copied,
 * or published without explicit permission. Misuse will lead to persecution.
 *
 * @copyright  2020 infomax websolutions GmbH
 * @link       http://www.infomax-it.de
 */

namespace de\codenamephp\deployer\mariadb\task;

use de\codenamephp\deployer\base\functions\All;
use de\codenamephp\deployer\base\functions\iAll;
use de\codenamephp\deployer\base\hostCheck\DoNotRunOnProduction;
use de\codenamephp\deployer\base\hostCheck\iHostCheck;
use de\codenamephp\deployer\base\hostCheck\SkippableByOption;
use de\codenamephp\deployer\mariadb\database\factory\database\iDatabase;
use de\codenamephp\deployer\mariadb\database\factory\database\SimpleNew;
use de\codenamephp\deployer\mariadb\dumpfile\factory\iDumpfile;
use de\codenamephp\deployer\mariadb\task\deleteDump\factory\iDeleteDump;
use de\codenamephp\deployer\mariadb\task\download\factory\iDownload;
use de\codenamephp\deployer\mariadb\task\dump\factory\iDump;
use de\codenamephp\deployer\mariadb\task\import\factory\iImport;

/**
 * A collection of tasks that pulls a remote database to local
 */
final class Pull implements iTask {

  public function __construct(
    public iDumpfile   $dumpfile = new \de\codenamephp\deployer\mariadb\dumpfile\factory\fromDatabaseNameAndTimestamp\SimpleNew(),
    public iDatabase   $database = new SimpleNew(),
    public iDump       $dump = new dump\factory\SimpleNew(),
    public iDeleteDump $deleteDump = new deleteDump\factory\SimpleNew(),
    public iImport     $import = new import\factory\SimpleNew(),
    public iDownload   $download = new download\factory\SimpleNew(),
    public iHostCheck  $hostCheck = new SkippableByOption(new DoNotRunOnProduction()),
    public iAll        $deployerFunctions = new All()
  ) {
  }

  /**
   * This task uses the 'database' configuration in the host to dump the database into a dumpfile on the remote, downloads it to local, removes the
   * remote dumpfile (cleanup after yourselves!) imports the local dump into the database and removes the local dump file
   *
   * @return void
   */
  public function __invoke() : void {
    $database = $this->database->fromConfigKey();
    $dumpfile = $this->dumpfile->create($database);

    $this->dump->create($database, $dumpfile)();
    $this->download->createWithSingleDumpfile($dumpfile)();
    $this->deleteDump->create($dumpfile)();

    $this->deployerFunctions->on($this->deployerFunctions->localhost(), function() use ($dumpfile) {
      $database = $this->database->fromConfigKey();
      $this->import->create($database, $dumpfile)();
      $this->deleteDump->create($dumpfile)();
    });
  }
}
