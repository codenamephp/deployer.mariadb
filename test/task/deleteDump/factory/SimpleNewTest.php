<?php declare(strict_types=1);

namespace de\codenamephp\deployer\mariadb\test\task\deleteDump\factory;

use de\codenamephp\deployer\mariadb\dumpfile\iDumpfile;
use de\codenamephp\deployer\mariadb\task\deleteDump\DeleteDump;
use de\codenamephp\deployer\mariadb\task\deleteDump\factory\SimpleNew;
use PHPUnit\Framework\TestCase;

final class SimpleNewTest extends TestCase {

  private SimpleNew $sut;

  protected function setUp() : void {
    parent::setUp();

    $this->sut = new SimpleNew();
  }

  public function testCreate() : void {
    $dumpfile = $this->createMock(iDumpfile::class);

    $deleteDump = $this->sut->create($dumpfile);

    self::assertInstanceOf(DeleteDump::class, $deleteDump);
    self::assertSame($dumpfile, $deleteDump->dumpfile);
  }
}
