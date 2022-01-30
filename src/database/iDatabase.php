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

namespace de\codenamephp\deployer\mariadb\database;

/**
 * Simple entity that stores database related configs such as user, password, ...
 */
interface iDatabase {

  public function getUser() : string;

  public function getPassword() : string;

  public function getName() : string;

  public function getHost() : string;

  public function getPort() : int;

  /**
   * @return string[]
   */
  public function getTablesToIgnore() : array;
}