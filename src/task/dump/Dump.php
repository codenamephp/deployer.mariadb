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

namespace de\codenamephp\deployer\mariadb\task\dump;

use de\codenamephp\deployer\base\functions\All;
use de\codenamephp\deployer\base\functions\iRun;
use de\codenamephp\deployer\mariadb\database\iDatabase;
use de\codenamephp\deployer\mariadb\dumpfile\iDumpfile;
use Deployer\Exception\Exception;
use Deployer\Exception\RunException;
use Deployer\Exception\TimeoutException;

final class Dump implements iDump {

  public function __construct(public iDatabase $database,
                              public iDumpfile $dumpfile,
                              public iRun      $deployerFunctions = new All()) {
  }

  /**
   * Uses the given database info to construct 2 mysqldump shell commands:
   *
   * The first only dumps the structure and the second only the data without the ignored tables. This has to be done in 2 steps since earlier versions of
   * mysql and mariadb only supported ignoring tables in their entirety but we need the structure of all tables but not all the data (e.g. imx_image)
   *
   * The first command creates/overwrites the dumpfile with the structure sql and the second command appends the data
   *
   * @return void
   * @throws Exception
   * @throws RunException
   * @throws TimeoutException
   */
  public function __invoke() : void {
    $dumpfile = $this->dumpfile->getFilename();

    $ignoredTableString = implode(' ', array_map(static fn(string $table) => sprintf('--ignore-table "%s"', $table), $this->database->getTablesToIgnore()));

    $name = $this->database->getName();

    $baseCommand = sprintf('mysqldump --user="%s" --password="%s" --host="%s" --port=%d --comments=false --disable-keys --no-autocommit --single-transaction', $this->database->getUser(), $this->database->getPassword(), $this->database->getHost(), $this->database->getPort());
    $this->deployerFunctions->run(sprintf('%s --add-drop-table --routines --no-data "%s" > "%s"', $baseCommand, $name, $dumpfile));
    $this->deployerFunctions->run(sprintf('%s --no-create-info --extended-insert %s "%s" >> "%s"', $baseCommand, $ignoredTableString, $name, $dumpfile));
  }
}
