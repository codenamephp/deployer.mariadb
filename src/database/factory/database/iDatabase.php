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

use de\codenamephp\deployer\base\MissingConfigurationException;
use de\codenamephp\deployer\mariadb\task\iTask;

/**
 * Simple abstract factory to create database instances
 */
interface iDatabase {

  public function create(string $user, string $password, string $name, array $tablesToIgnore = [], ?string $host = null, ?int $port = null) : \de\codenamephp\deployer\mariadb\database\iDatabase;

  /**
   * Creates an entity from an array. Implemtations should support these values:
   * - user (string|required)
   * - password (string|required)
   * - name (string|required)
   * - host (string)
   * - port (int)
   * - tablesToIgnore (string[])
   *
   * @param array $config
   * @return \de\codenamephp\deployer\mariadb\database\iDatabase
   */
  public function fromArray(array $config) : \de\codenamephp\deployer\mariadb\database\iDatabase;

  /**
   * Uses a config key to get the needed data and create the db instance
   *
   * @param string $configKey Usually the key where the config can be found in deployer
   * @return \de\codenamephp\deployer\mariadb\database\iDatabase
   * @throws MissingConfigurationException if the config for the key was not set or empty
   */
  public function fromConfigKey(string $configKey = iTask::CONFIG_KEY_DATABASE) : \de\codenamephp\deployer\mariadb\database\iDatabase;
}