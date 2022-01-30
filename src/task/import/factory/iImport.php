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

namespace de\codenamephp\deployer\mariadb\task\import\factory;

use de\codenamephp\deployer\mariadb\database\iDatabase;
use de\codenamephp\deployer\mariadb\dumpfile\iDumpfile;

/**
 * Interface for abstract factories to create instances of \de\imxnet\deployer\mariadb\task\import\iImport at runtime so they can be used in other tasks
 */
interface iImport {

  public function create(iDatabase $database, iDumpfile $dumpfile) : \de\codenamephp\deployer\mariadb\task\import\iImport;
}
