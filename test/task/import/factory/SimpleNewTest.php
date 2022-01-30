<?php declare(strict_types=1);

namespace de\codenamephp\deployer\mariadb\test\task\import\factory;

use de\codenamephp\deployer\mariadb\database\iDatabase;
use de\codenamephp\deployer\mariadb\dumpfile\iDumpfile;
use de\codenamephp\deployer\mariadb\task\import\factory\SimpleNew;
use de\codenamephp\deployer\mariadb\task\import\Import;
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

    $import = $this->sut->create($database, $dumpfile);

    self::assertInstanceOf(Import::class, $import);
    self::assertSame($database, $import->database);
    self::assertSame($dumpfile, $dumpfile);
  }
}
