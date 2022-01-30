<?php declare(strict_types=1);

namespace de\codenamephp\deployer\mariadb\test\task\download\factory;

use de\codenamephp\deployer\mariadb\dumpfile\iDumpfile;
use de\codenamephp\deployer\mariadb\task\download\Download;
use de\codenamephp\deployer\mariadb\task\download\factory\SimpleNew;
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

    self::assertInstanceOf(Download::class, $download);
    self::assertSame($remote, $download->remoteDumpfile);
    self::assertSame($local, $download->localDumpfile);
  }

  public function testCreateWithSingleDumpfile() : void {
    $dumpfile = $this->createMock(iDumpfile::class);

    $download = $this->sut->createWithSingleDumpfile($dumpfile);

    self::assertInstanceOf(Download::class, $download);
    self::assertSame($dumpfile, $download->remoteDumpfile);
    self::assertSame($dumpfile, $download->localDumpfile);
  }
}
