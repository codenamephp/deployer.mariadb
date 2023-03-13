<?php declare(strict_types=1);

namespace de\codenamephp\deployer\mariadb\test\task\dump;

use de\codenamephp\deployer\base\functions\All;
use de\codenamephp\deployer\base\functions\iRun;
use de\codenamephp\deployer\mariadb\database\iDatabase;
use de\codenamephp\deployer\mariadb\database\Immutable;
use de\codenamephp\deployer\mariadb\dumpfile\iDumpfile;
use de\codenamephp\deployer\mariadb\task\dump\Dump;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

final class DumpTest extends TestCase {

  use MockeryPHPUnitIntegration;

  private Dump $sut;

  protected function setUp() : void {
    parent::setUp();

    $database = $this->createMock(iDatabase::class);
    $dumpfile = $this->createMock(iDumpfile::class);

    $this->sut = new Dump($database, $dumpfile);
  }

  public function test__construct() : void {
    $database = $this->createMock(iDatabase::class);
    $dumpfile = $this->createMock(iDumpfile::class);

    $this->sut = new Dump($database, $dumpfile);

    self::assertSame($database, $this->sut->database);
    self::assertSame($dumpfile, $this->sut->dumpfile);
    self::assertInstanceOf(All::class, $this->sut->deployerFunctions);
  }

  public function test__invoke() : void {
    $this->sut->database = new Immutable('myUser', 'topSecret', 'myDatabase', ['ignore1', 'ignore2'], 'someHost', 1234);
    $this->sut->dumpfile = $this->createConfiguredMock(iDumpfile::class, ['getFilename' => '/some/folder/file']);

    $this->sut->deployerFunctions = \Mockery::mock(iRun::class);
    $this->sut->deployerFunctions->allows('run')->once()->ordered()->with('mysqldump --user=myUser --password=topSecret --host=someHost --port=1234 --comments=false --disable-keys --no-autocommit --single-transaction --add-drop-table --routines --no-data myDatabase > /some/folder/file');
    $this->sut->deployerFunctions->allows('run')->once()->ordered()->with('mysqldump --user=myUser --password=topSecret --host=someHost --port=1234 --comments=false --disable-keys --no-autocommit --single-transaction --no-create-info --extended-insert --ignore-table ignore1 --ignore-table ignore2 myDatabase >> /some/folder/file');

    $this->sut->__invoke();
  }
}
