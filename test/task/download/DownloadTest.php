<?php declare(strict_types=1);

namespace de\codenamephp\deployer\mariadb\test\task\download;

use de\codenamephp\deployer\base\functions\All;
use de\codenamephp\deployer\base\functions\iDownload;
use de\codenamephp\deployer\mariadb\dumpfile\iDumpfile;
use de\codenamephp\deployer\mariadb\task\download\Download;
use PHPUnit\Framework\TestCase;

final class DownloadTest extends TestCase {

  private Download $sut;

  protected function setUp() : void {
    parent::setUp();

    $remoteDumpfile = $this->createMock(iDumpfile::class);
    $localDumpfile = $this->createMock(iDumpfile::class);

    $this->sut = new Download($remoteDumpfile, $localDumpfile);
  }

  public function test__construct() : void {
    $remoteDumpfile = $this->createMock(iDumpfile::class);
    $localDumpfile = $this->createMock(iDumpfile::class);

    $this->sut = new Download($remoteDumpfile, $localDumpfile);

    self::assertSame($remoteDumpfile, $this->sut->remoteDumpfile);
    self::assertSame($localDumpfile, $this->sut->localDumpfile);
    self::assertInstanceOf(All::class, $this->sut->deployerFunctions);
  }

  public function test__invoke() : void {
    $this->sut->localDumpfile = $this->createConfiguredMock(iDumpfile::class, ['getName' => 'local']);
    $this->sut->remoteDumpfile = $this->createConfiguredMock(iDumpfile::class, ['getName' => 'remote']);

    $this->sut->deployerFunctions = $this->createMock(iDownload::class);
    $this->sut->deployerFunctions->expects(self::once())->method('download')->with('remote', 'local');

    $this->sut->__invoke();
  }
}
