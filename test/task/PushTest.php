<?php declare(strict_types=1);

namespace de\codenamephp\deployer\mariadb\test\task;

use Closure;
use de\codenamephp\deployer\base\functions\All;
use de\codenamephp\deployer\base\functions\iAll;
use de\codenamephp\deployer\base\hostCheck\DoNotRunOnProduction;
use de\codenamephp\deployer\base\hostCheck\iHostCheck;
use de\codenamephp\deployer\mariadb\database\factory\database\iDatabase;
use de\codenamephp\deployer\mariadb\dumpfile\factory\fromDatabaseNameAndTimestamp\SimpleNew;
use de\codenamephp\deployer\mariadb\dumpfile\factory\iDumpfile;
use de\codenamephp\deployer\mariadb\task\deleteDump\factory\iDeleteDump;
use de\codenamephp\deployer\mariadb\task\dump\factory\iDump;
use de\codenamephp\deployer\mariadb\task\import\factory\iImport;
use de\codenamephp\deployer\mariadb\task\Push;
use de\codenamephp\deployer\mariadb\task\upload\factory\iUpload;
use Deployer\Host\Localhost;
use PHPUnit\Framework\TestCase;

final class PushTest extends TestCase {

  private Push $sut;

  protected function setUp() : void {
    parent::setUp();

    $dumpfile = $this->createMock(iDumpfile::class);
    $database = $this->createMock(iDatabase::class);
    $dump = $this->createMock(iDump::class);
    $deleteDump = $this->createMock(iDeleteDump::class);
    $import = $this->createMock(iImport::class);
    $upload = $this->createMock(iUpload::class);
    $hostCheck = $this->createMock(iHostCheck::class);
    $deployerFunctions = $this->createMock(iAll::class);

    $this->sut = new Push($dumpfile, $database, $dump, $deleteDump, $import, $upload, $hostCheck, $deployerFunctions);
  }

  public function test__construct() : void {
    $this->sut = new Push();

    self::assertInstanceOf(SimpleNew::class, $this->sut->dumpfile);
    self::assertInstanceOf(\de\codenamephp\deployer\mariadb\database\factory\database\SimpleNew::class, $this->sut->database);
    self::assertInstanceOf(\de\codenamephp\deployer\mariadb\task\dump\factory\SimpleNew::class, $this->sut->dump);
    self::assertInstanceOf(\de\codenamephp\deployer\mariadb\task\deleteDump\factory\SimpleNew::class, $this->sut->deleteDump);
    self::assertInstanceOf(\de\codenamephp\deployer\mariadb\task\import\factory\SimpleNew::class, $this->sut->import);
    self::assertInstanceOf(\de\codenamephp\deployer\mariadb\task\upload\factory\SimpleNew::class, $this->sut->upload);
    self::assertInstanceOf(DoNotRunOnProduction::class, $this->sut->hostCheck);
    self::assertInstanceOf(All::class, $this->sut->deployerFunctions);
  }

  public function test__invoke() : void {
    $this->sut->hostCheck = $this->createMock(iHostCheck::class);
    $this->sut->hostCheck->expects(self::once())->method('check');

    $localhost = $this->createMock(Localhost::class);

    $this->sut->deployerFunctions = $this->createMock(iAll::class);
    $this->sut->deployerFunctions->expects(self::once())->method('localhost')->willReturn($localhost);
    $this->sut->deployerFunctions
      ->expects(self::exactly(2))
      ->method('on')
      ->with($localhost)
      ->willReturnCallback(static fn(Localhost $hosts, Closure $callback) => $callback());

    $remoteDatabase = $this->createMock(\de\codenamephp\deployer\mariadb\database\iDatabase::class);
    $localDatabase = $this->createMock(\de\codenamephp\deployer\mariadb\database\iDatabase::class);

    $this->sut->database = $this->createMock(iDatabase::class);
    $this->sut->database->expects(self::exactly(2))->method('fromConfigKey')->willReturnOnConsecutiveCalls($remoteDatabase, $localDatabase);

    $dumpfile = $this->createMock(\de\codenamephp\deployer\mariadb\dumpfile\iDumpfile::class);

    $this->sut->dumpfile = $this->createMock(iDumpfile::class);
    $this->sut->dumpfile->expects(self::once())->method('create')->with($remoteDatabase)->willReturn($dumpfile);

    $dumpTask = $this->createMock(\de\codenamephp\deployer\mariadb\task\dump\iDump::class);
    $dumpTask->expects(self::once())->method('__invoke');

    $this->sut->dump = $this->createMock(iDump::class);
    $this->sut->dump->expects(self::once())->method('create')->with($localDatabase, $dumpfile)->willReturn($dumpTask);

    $uploadTask = $this->createMock(\de\codenamephp\deployer\mariadb\task\upload\iUpload::class);
    $uploadTask->expects(self::once())->method('__invoke');

    $this->sut->upload = $this->createMock(iUpload::class);
    $this->sut->upload->expects(self::once())->method('createWithSingleDumpfile')->with($dumpfile)->willReturn($uploadTask);

    $deleteLocalDumpTask = $this->createMock(\de\codenamephp\deployer\mariadb\task\deleteDump\iDeleteDump::class);
    $deleteLocalDumpTask->expects(self::once())->method('__invoke');
    $deleteRemoteDumpTask = $this->createMock(\de\codenamephp\deployer\mariadb\task\deleteDump\iDeleteDump::class);
    $deleteRemoteDumpTask->expects(self::once())->method('__invoke');

    $this->sut->deleteDump = $this->createMock(iDeleteDump::class);
    $this->sut->deleteDump->expects(self::exactly(2))->method('create')->with($dumpfile)->willReturnOnConsecutiveCalls($deleteLocalDumpTask, $deleteRemoteDumpTask);

    $importTask = $this->createMock(\de\codenamephp\deployer\mariadb\task\import\iImport::class);
    $importTask->expects(self::once())->method('__invoke');

    $this->sut->import = $this->createMock(iImport::class);
    $this->sut->import->expects(self::once())->method('create')->with($remoteDatabase, $dumpfile)->willReturn($importTask);

    $this->sut->__invoke();
  }
}
