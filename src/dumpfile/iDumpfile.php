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

namespace de\codenamephp\deployer\mariadb\dumpfile;

/**
 * Simple entity that represents a name for the database dumpfile e.g. created from the database name and a timestamp to avoid name collisions or a manual
 * string
 */
interface iDumpfile {

  /**
   * Gets the filename of the dumpfile
   *
   * @return string
   */
  public function getName() : string;

  /**
   * In most implementations this should be an alias to getName()
   *
   * @return string
   */
  public function __toString() : string;
}