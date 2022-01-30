<?php declare(strict_types=1);

namespace de\codenamephp\deployer\mariadb\test\task\upload;

use de\codenamephp\deployer\base\functions\All;
use de\codenamephp\deployer\base\functions\iUpload;
use de\codenamephp\deployer\mariadb\dumpfile\iDumpfile;
use de\codenamephp\deployer\mariadb\task\upload\Upload;
use PHPUnit\Framework\TestCase;

final class UploadTest extends TestCase {

  private Upload $sut;

  protected function setUp() : void {
    parent::setUp();

    $remoteDumpfile = $this->createMock(iDumpfile::class);
    $localDumpfile = $this->createMock(iDumpfile::class);

    $this->sut = new Upload($remoteDumpfile, $localDumpfile);
  }

  public function test__construct() : void {
    $remoteDumpfile = $this->createMock(iDumpfile::class);
    $localDumpfile = $this->createMock(iDumpfile::class);

    $this->sut = new Upload($remoteDumpfile, $localDumpfile);

    self::assertSame($remoteDumpfile, $this->sut->remoteDumpfile);
    self::assertSame($localDumpfile, $this->sut->localDumpfile);
    self::assertInstanceOf(All::class, $this->sut->deployerFunctions);
  }

  public function test__invoke() : void {
    $this->sut->localDumpfile = $this->createConfiguredMock(iDumpfile::class, ['getName' => 'local']);
    $this->sut->remoteDumpfile = $this->createConfiguredMock(iDumpfile::class, ['getName' => 'remote']);

    $this->sut->deployerFunctions = $this->createMock(iUpload::class);
    $this->sut->deployerFunctions->expects(self::once())->method('upload')->with('local', 'remote');

    $this->sut->__invoke();
  }
}
