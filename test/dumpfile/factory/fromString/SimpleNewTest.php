<?php declare(strict_types=1);

namespace de\codenamephp\deployer\mariadb\test\dumpfile\factory\fromString;

use de\codenamephp\deployer\mariadb\database\iDatabase;
use de\codenamephp\deployer\mariadb\dumpfile\factory\fromString\SimpleNew;
use de\codenamephp\deployer\mariadb\dumpfile\FromString;
use PHPUnit\Framework\TestCase;

final class SimpleNewTest extends TestCase {

  private SimpleNew $sut;

  protected function setUp() : void {
    parent::setUp();

    $this->sut = new SimpleNew();
  }

  public function testCreate() : void {
    $database = $this->createMock(iDatabase::class);
    $database->expects(self::once())->method('getName')->willReturn('some database');

    $dumpfile = $this->sut->create($database);

    self::assertInstanceOf(FromString::class, $dumpfile);
    self::assertEquals('some database', $dumpfile->getName());
  }
}
