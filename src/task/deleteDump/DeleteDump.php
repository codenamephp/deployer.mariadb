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

namespace de\codenamephp\deployer\mariadb\task\deleteDump;

use de\codenamephp\deployer\base\functions\All;
use de\codenamephp\deployer\base\functions\iRun;
use de\codenamephp\deployer\mariadb\dumpfile\iDumpfile;
use Deployer\Exception\Exception;
use Deployer\Exception\RunException;
use Deployer\Exception\TimeoutException;

/**
 * Task to delete the given dumpfile using a shell command
 */
final class DeleteDump implements iDeleteDump {

  public function __construct(public iDumpfile $dumpfile, public iRun $deployerFunctions = new All()) { }

  /**
   * Just executes rm with the dumpfile name on the current host
   *
   * @return void
   * @throws Exception
   * @throws RunException
   * @throws TimeoutException
   */
  public function __invoke() : void {
    $this->deployerFunctions->run(sprintf('rm %s', $this->dumpfile->getFilename()));
  }
}
