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

namespace de\codenamephp\deployer\mariadb\task\dump;

use de\codenamephp\deployer\mariadb\task\iTask;

/**
 * Interface to dump the database so it can be further processed (changed, zipped, downloaded, ...)
 *
 * Implementations should use \de\imxnet\deployer\mariadb\database\iDatabase to get the database credentials
 * and \de\imxnet\deployer\mariadb\dumpfile\iDumpfile instances to make sure to write to the correct files
 */
interface iDump extends iTask { }