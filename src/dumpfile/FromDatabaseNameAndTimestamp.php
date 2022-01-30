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

use DateTimeImmutable;
use DateTimeInterface;
use de\codenamephp\deployer\mariadb\database\iDatabase;

/**
 * Simple entity that represents a name for the database dumpfile created from the database name and a timestamp to avoid name collisions
 */
final class FromDatabaseNameAndTimestamp implements iDumpfile {

  private string $name;

  private DateTimeInterface $now;

  public function __construct(iDatabase $database, DateTimeInterface $now = null) {
    $this->setNow($now ?? new DateTimeImmutable());
    $this->setName(sprintf('databasedump_%s_%d', $database->getName(), $this->getNow()->format('YmdHis')));
  }

  public function getNow() : DateTimeInterface {
    return $this->now;
  }

  public function setNow(DateTimeInterface $now) : self {
    $this->now = $now;
    return $this;
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
