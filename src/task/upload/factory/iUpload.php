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

namespace de\codenamephp\deployer\mariadb\task\upload\factory;

use de\codenamephp\deployer\mariadb\dumpfile\iDumpfile;

/**
 * Interface for abstract factories to create instances of \de\imxnet\deployer\mariadb\task\upload\iUpload at runtime so they can be used in other tasks
 */
interface iUpload {

  public function create(iDumpfile $remote, iDumpfile $local) : \de\codenamephp\deployer\mariadb\task\upload\iUpload;

  /**
   * Often we just use the same dumpfile name on local and remote. This method makes the creation easier and should pass the same dumpfile instance along
   * as local and remote.
   *
   * @param iDumpfile $dumpfile
   * @return \de\codenamephp\deployer\mariadb\task\upload\iUpload
   */
  public function createWithSingleDumpfile(iDumpfile $dumpfile) : \de\codenamephp\deployer\mariadb\task\upload\iUpload;
}
