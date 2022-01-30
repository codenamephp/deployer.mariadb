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
 * Simple immutable entity that stores database related configs such as user, password, ...
 *
 * @psalm-suppress InvalidNullableReturnType,NullableReturnStatement psalm doesn't detect the fallback in the constructor correctly
 */
final class Immutable implements iDatabase {

  /**
   *
   * @param string $user
   * @param string $password
   * @param string $name
   * @param string[] $tablesToIgnore
   * @param string|null $host
   * @param int|null $port
   */
  public function __construct(private string  $user,
                              private string  $password,
                              private string  $name,
                              private array   $tablesToIgnore = [],
                              private ?string $host = null,
                              private ?int    $port = null) {
    $this->host = $host ?? 'localhost';
    $this->port = $port ?? 3306;
  }

  public function getUser() : string {
    return $this->user;
  }

  public function getPassword() : string {
    return $this->password;
  }

  public function getName() : string {
    return $this->name;
  }

  public function getHost() : string {
    return $this->host;
  }

  public function getPort() : int {
    return $this->port;
  }

  /**
   * @return string[]
   */
  public function getTablesToIgnore() : array {
    return $this->tablesToIgnore;
  }
}
