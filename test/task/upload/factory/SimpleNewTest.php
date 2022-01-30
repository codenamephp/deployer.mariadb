<?php declare(strict_types=1);

namespace de\codenamephp\deployer\mariadb\test\task\upload\factory;

use de\codenamephp\deployer\mariadb\dumpfile\iDumpfile;
use de\codenamephp\deployer\mariadb\task\upload\factory\SimpleNew;
use de\codenamephp\deployer\mariadb\task\upload\Upload;
use PHPUnit\Framework\TestCase;

final class SimpleNewTest extends TestCase {

  private SimpleNew $sut;

  protected function setUp() : void {
    parent::setUp();

    $this->sut = new SimpleNew();
  }

  public function testCreate() : void {
    $remote = $this->createMock(iDumpfile::class);
    $local = $this->createMock(iDumpfile::class);

    $download = $this->sut->create($remote, $local);

    self::assertInstanceOf(Upload::class, $download);
    self::assertSame($remote, $download->remoteDumpfile);
    self::assertSame($local, $download->localDumpfile);
  }

  public function testCreateWithSingleDumpfile() : void {
    $dumpfile = $this->createMock(iDumpfile::class);

    $download = $this->sut->createWithSingleDumpfile($dumpfile);

    self::assertInstanceOf(Upload::class, $download);
    self::assertSame($dumpfile, $download->remoteDumpfile);
    self::assertSame($dumpfile, $download->localDumpfile);
  }
}
