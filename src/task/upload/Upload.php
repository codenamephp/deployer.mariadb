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

namespace de\codenamephp\deployer\mariadb\task\upload;

use de\codenamephp\deployer\base\functions\All;
use de\codenamephp\deployer\mariadb\dumpfile\iDumpfile;
use Deployer\Exception\RunException;

/**
 * Uploads a dumpfile from local to remote
 */
final class Upload implements iUpload {

  public function __construct(public iDumpfile                                       $remoteDumpfile,
                              public iDumpfile                                       $localDumpfile,
                              public \de\codenamephp\deployer\base\functions\iUpload $deployerFunctions = new All()) {
  }

  /**
   * Uses the built in deployer upload function to transfer the local dump file to remote
   *
   * @return void
   * @throws RunException
   */
  public function __invoke() : void {
    $this->deployerFunctions->upload($this->localDumpfile->getFilename(), $this->remoteDumpfile->getFilename());
  }
}