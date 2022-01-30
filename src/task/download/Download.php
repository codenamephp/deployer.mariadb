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

namespace de\codenamephp\deployer\mariadb\task\download;

use de\codenamephp\deployer\base\functions\All;
use de\codenamephp\deployer\base\task\iTask;
use de\codenamephp\deployer\mariadb\dumpfile\iDumpfile;
use Deployer\Exception\RunException;

final class Download implements iTask, iDownload {

  public function __construct(public iDumpfile                                         $remoteDumpfile,
                              public iDumpfile                                         $localDumpfile,
                              public \de\codenamephp\deployer\base\functions\iDownload $deployerFunctions = new All()) {
  }

  /**
   * Uses the deployer download function to transfer the remote dumpfile to local
   *
   * @return void
   * @throws RunException
   */
  public function __invoke() : void {
    $this->deployerFunctions->download($this->remoteDumpfile->getFilename(), $this->localDumpfile->getFilename());
  }
}
