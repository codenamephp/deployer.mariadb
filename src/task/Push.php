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
use de\codenamephp\deployer\base\UnsafeOperationException;
use de\codenamephp\deployer\mariadb\database\factory\database\iDatabase;
use de\codenamephp\deployer\mariadb\database\factory\database\SimpleNew;
use de\codenamephp\deployer\mariadb\dumpfile\factory\iDumpfile;
use de\codenamephp\deployer\mariadb\task\deleteDump\factory\iDeleteDump;
use de\codenamephp\deployer\mariadb\task\dump\factory\iDump;
use de\codenamephp\deployer\mariadb\task\import\factory\iImport;
use de\codenamephp\deployer\mariadb\task\upload\factory\iUpload;

/**
 * Pushes the local database to the remote system so local changes will overwrite the remote ones
 */
final class Push implements iTask {

  public function __construct(
    public iDumpfile   $dumpfile = new \de\codenamephp\deployer\mariadb\dumpfile\factory\fromDatabaseNameAndTimestamp\SimpleNew(),
    public iDatabase   $database = new SimpleNew(),
    public iDump       $dump = new dump\factory\SimpleNew(),
    public iDeleteDump $deleteDump = new deleteDump\factory\SimpleNew(),
    public iImport     $import = new import\factory\SimpleNew(),
    public iUpload     $upload = new upload\factory\SimpleNew(),
    public iHostCheck  $hostCheck = new DoNotRunOnProduction(),
    public iAll        $deployerFunctions = new All()
  ) {
  }

  /**
   * This task uses the 'database' configuration in the localhost to dump the database into a dumpfile, uploads it to the remote, removes the
   * local dumpfile (cleanup after yourselves!) imports the dump into the database and removes the remote dump file
   *
   * @return void
   * @throws UnsafeOperationException when trying to push to production stage
   */
  public function __invoke() : void {
    $this->hostCheck->check();

    $localhost = $this->deployerFunctions->localhost();
    $database = $this->database->fromConfigKey();
    $dumpfile = $this->dumpfile->create($database);

    $this->deployerFunctions->on($localhost, fn() => $this->dump->create($this->database->fromConfigKey(), $dumpfile)());

    $this->upload->createWithSingleDumpfile($dumpfile)();

    $this->deployerFunctions->on($localhost, fn() => $this->deleteDump->create($dumpfile)());

    $this->import->create($database, $dumpfile)();
    $this->deleteDump->create($dumpfile)();
  }
}