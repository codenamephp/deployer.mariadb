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
use de\codenamephp\deployer\mariadb\task\upload\Upload;

final class SimpleNew implements iUpload {

  public function create(iDumpfile $remote, iDumpfile $local) : \de\codenamephp\deployer\mariadb\task\upload\iUpload {
    return new Upload($remote, $local);
  }

  public function createWithSingleDumpfile(iDumpfile $dumpfile) : \de\codenamephp\deployer\mariadb\task\upload\iUpload {
    return $this->create($dumpfile, $dumpfile);
  }
}
