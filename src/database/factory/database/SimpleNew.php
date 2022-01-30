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

namespace de\codenamephp\deployer\mariadb\database\factory\database;

use de\codenamephp\deployer\base\functions\All;
use de\codenamephp\deployer\base\functions\iGet;
use de\codenamephp\deployer\base\MissingConfigurationException;
use de\codenamephp\deployer\mariadb\database\Immutable;
use de\codenamephp\deployer\mariadb\task\iTask;

final class SimpleNew implements iDatabase {

  public function __construct(public iGet $deployerFunctions = new All()) { }

  public function create(string $user, string $password, string $name, array $tablesToIgnore = [], ?string $host = null, ?int $port = null) : \de\codenamephp\deployer\mariadb\database\iDatabase {
    return new Immutable($user, $password, $name, array_filter(array_map(static fn($value) => (string) $value, $tablesToIgnore)), $host, $port);
  }

  public function fromArray(array $config) : \de\codenamephp\deployer\mariadb\database\iDatabase {
    if(!array_key_exists('user', $config)) throw new MissingConfigurationException('User not set in database config');
    if(!array_key_exists('password', $config)) throw new MissingConfigurationException('Password not set in database config');
    if(!array_key_exists('name', $config)) throw new MissingConfigurationException('Name not set in database config');

    return new Immutable(
      (string) $config['user'],
      (string) $config['password'],
      (string) $config['name'],
      array_key_exists('tablesToIgnore', $config) ? array_filter(array_map(static fn($value) => (string) $value, (array) $config['tablesToIgnore'])) : [],
      array_key_exists('host', $config) ? (string) $config['host'] : null,
      array_key_exists('port', $config) ? (int) $config['port'] : null,
    );
  }

  public function fromConfigKey(string $configKey = iTask::CONFIG_KEY_DATABASE) : \de\codenamephp\deployer\mariadb\database\iDatabase {
    $databaseConfig = (array) $this->deployerFunctions->get($configKey);
    if($databaseConfig === []) throw new MissingConfigurationException('Database config was not set or empty');

    return $this->fromArray($databaseConfig);
  }
}