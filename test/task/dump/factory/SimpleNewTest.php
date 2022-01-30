<?php declare(strict_types=1);

namespace de\codenamephp\deployer\mariadb\test\task\dump\factory;

use de\codenamephp\deployer\mariadb\database\iDatabase;
use de\codenamephp\deployer\mariadb\dumpfile\iDumpfile;
use de\codenamephp\deployer\mariadb\task\dump\Dump;
use de\codenamephp\deployer\mariadb\task\dump\factory\SimpleNew;
use PHPUnit\Framework\TestCase;

final class SimpleNewTest extends TestCase {

  private SimpleNew $sut;

  protected function setUp() : void {
    parent::setUp();

    $this->sut = new SimpleNew();
  }

  public function testCreate() : void {
    $database = $this->createMock(iDatabase::class);
    $dumpfile = $this->createMock(iDumpfile::class);

    $dump = $this->sut->create($database, $dumpfile);

    self::assertInstanceOf(Dump::class, $dump);
    self::assertSame($database, $dump->database);
    self::assertSame($dumpfile, $dump->dumpfile);
  }
}
