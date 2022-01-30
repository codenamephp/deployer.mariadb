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

namespace de\codenamephp\deployer\mariadb\task\import;

use de\codenamephp\deployer\base\functions\All;
use de\codenamephp\deployer\base\functions\iRun;
use de\codenamephp\deployer\mariadb\database\iDatabase;
use de\codenamephp\deployer\mariadb\dumpfile\iDumpfile;
use Deployer\Exception\Exception;
use Deployer\Exception\RunException;
use Deployer\Exception\TimeoutException;

/**
 * Task to import a dumpfile into mysql
 */
final class Import implements iImport {

  public function __construct(public iDatabase $database,
                              public iDumpfile $dumpfile,
                              public iRun      $deployerFunctions = new All()) {
  }

  /**
   * Uses the database info to run 2 mysql commands:
   *
   * The first creates the database if it does not yet exist
   * The second imports the dumpfile into the database
   *
   * @return void
   * @throws Exception
   * @throws RunException
   * @throws TimeoutException
   */
  public function __invoke() : void {
    $name = $this->database->getName();
    $baseCommand = sprintf('mysql --user=%s --password=%s --host=%s --port=%s', $this->database->getUser(), $this->database->getPassword(), $this->database->getHost(), $this->database->getPort());
    $this->deployerFunctions->run(sprintf('%s -e "CREATE DATABASE IF NOT EXISTS %s;"', $baseCommand, $name));
    $this->deployerFunctions->run(sprintf('%s %s < %s;', $baseCommand, $name, $this->dumpfile->getName()));
  }
}
