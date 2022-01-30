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

namespace de\codenamephp\deployer\mariadb\task;

/**
 * Base interface for database task mainly exists for marking and constants
 */
interface iTask extends \de\codenamephp\deployer\base\task\iTask {

  public const CONFIG_KEY_DATABASE = 'database';
}
