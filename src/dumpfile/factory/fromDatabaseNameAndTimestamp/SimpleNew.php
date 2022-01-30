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

namespace de\codenamephp\deployer\mariadb\dumpfile\factory\fromDatabaseNameAndTimestamp;

use de\codenamephp\deployer\mariadb\database\iDatabase;
use de\codenamephp\deployer\mariadb\dumpfile\FromDatabaseNameAndTimestamp;
use de\codenamephp\deployer\mariadb\dumpfile\iDumpfile;

final class SimpleNew implements \de\codenamephp\deployer\mariadb\dumpfile\factory\iDumpfile {

  public function __construct(public string $dumpfileFolder = '{{deploy_path}}') { }

  public function create(iDatabase $database) : iDumpfile {
    return new FromDatabaseNameAndTimestamp($database, $this->dumpfileFolder);
  }
}
