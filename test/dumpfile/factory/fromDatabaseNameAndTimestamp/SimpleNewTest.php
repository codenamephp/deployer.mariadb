<?php declare(strict_types=1);

namespace de\codenamephp\deployer\mariadb\test\dumpfile\factory\fromDatabaseNameAndTimestamp;

use de\codenamephp\deployer\mariadb\database\iDatabase;
use de\codenamephp\deployer\mariadb\dumpfile\factory\fromDatabaseNameAndTimestamp\SimpleNew;
use de\codenamephp\deployer\mariadb\dumpfile\FromDatabaseNameAndTimestamp;
use PHPUnit\Framework\TestCase;

final class SimpleNewTest extends TestCase {

  private SimpleNew $sut;

  protected function setUp() : void {
    parent::setUp();

    $this->sut = new SimpleNew();
  }

  public function testCreate() : void {
    self::assertInstanceOf(FromDatabaseNameAndTimestamp::class, $this->sut->create($this->createMock(iDatabase::class)));
  }
}
