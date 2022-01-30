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
 * Implementation that just uses the given string as file name
 */
final class FromString implements iDumpfile {

  private string $name;

  public function __construct(string $name) {
    $this->setName($name);
  }

  public function getName() : string {
    return $this->name;
  }

  public function __toString() : string {
    return $this->getName();
  }

  private function setName(string $name) : iDumpfile {
    $this->name = $name;
    return $this;
  }
}