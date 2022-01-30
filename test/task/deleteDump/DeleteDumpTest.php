<?php declare(strict_types=1);

namespace de\codenamephp\deployer\mariadb\test\task\deleteDump;

use de\codenamephp\deployer\base\functions\All;
use de\codenamephp\deployer\base\functions\iRun;
use de\codenamephp\deployer\mariadb\dumpfile\iDumpfile;
use de\codenamephp\deployer\mariadb\task\deleteDump\DeleteDump;
use PHPUnit\Framework\TestCase;

final class DeleteDumpTest extends TestCase {

  private DeleteDump $sut;

  protected function setUp() : void {
    parent::setUp();

    $dumpfile = $this->createMock(iDumpfile::class);

    $this->sut = new DeleteDump($dumpfile);
  }

  public function test__construct() : void {
    $dumpfile = $this->createMock(iDumpfile::class);

    $this->sut = new DeleteDump($dumpfile);

    self::assertSame($dumpfile, $this->sut->dumpfile);
    self::assertInstanceOf(All::class, $this->sut->deployerFunctions);
  }

  public function test__invoke() : void {
    $this->sut->dumpfile = $this->createMock(iDumpfile::class);
    $this->sut->dumpfile->expects(self::once())->method('getName')->willReturn('some-dump-file');

    $this->sut->deployerFunctions = $this->createMock(iRun::class);
    $this->sut->deployerFunctions->expects(self::once())->method('run')->with('rm some-dump-file');

    $this->sut->__invoke();
  }
}
