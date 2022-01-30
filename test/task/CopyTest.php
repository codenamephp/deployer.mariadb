<?php declare(strict_types=1);

namespace de\codenamephp\deployer\mariadb\test\task;

use Closure;
use de\codenamephp\deployer\base\functions\iAll;
use de\codenamephp\deployer\base\functions\iInput;
use de\codenamephp\deployer\base\hostCheck\iHostCheck;
use de\codenamephp\deployer\base\iConfigurationKeys;
use de\codenamephp\deployer\base\MissingInputException;
use de\codenamephp\deployer\mariadb\database\factory\database\iDatabase;
use de\codenamephp\deployer\mariadb\database\factory\database\SimpleNew;
use de\codenamephp\deployer\mariadb\dumpfile\factory\iDumpfile;
use de\codenamephp\deployer\mariadb\task\Copy;
use de\codenamephp\deployer\mariadb\task\deleteDump\factory\iDeleteDump;
use de\codenamephp\deployer\mariadb\task\download\factory\iDownload;
use de\codenamephp\deployer\mariadb\task\dump\factory\iDump;
use de\codenamephp\deployer\mariadb\task\import\factory\iImport;
use de\codenamephp\deployer\mariadb\task\upload\factory\iUpload;
use Deployer\Host\Host;
use Deployer\Host\Localhost;
use PHPUnit\Framework\TestCase;

final class CopyTest extends TestCase {

  private Copy $sut;

  protected function setUp() : void {
    parent::setUp();

    $dumpfile = $this->createMock(iDumpfile::class);
    $database = $this->createMock(iDatabase::class);
    $dump = $this->createMock(iDump::class);
    $deleteDump = $this->createMock(iDeleteDump::class);
    $import = $this->createMock(iImport::class);
    $download = $this->createMock(iDownload::class);
    $upload = $this->createMock(iUpload::class);
    $hostCheck = $this->createMock(iHostCheck::class);
    $deployerFunctions = $this->createMock(iAll::class);

    $this->sut = new Copy($dumpfile, $database, $dump, $deleteDump, $import, $download, $upload, $hostCheck, $deployerFunctions);
  }

  public function test__construct() : void {
    $hostCheck = $this->createMock(iHostCheck::class);

    $deployerFunctions = $this->createMock(iAll::class);
    $deployerFunctions->expects(self::once())->method('option')->with(
      Copy::DB_SOURCE_HOST,
      null,
      iInput::OPTION_VALUE_REQUIRED,
      'The source host to copy the database(s) from',
      iConfigurationKeys::PRODUCTION
    );

    $this->sut = new Copy(hostCheck: $hostCheck, deployerFunctions: $deployerFunctions);

    self::assertSame($deployerFunctions, $this->sut->deployerFunctions);
    self::assertInstanceOf(SimpleNew::class, $this->sut->database);
    self::assertInstanceOf(\de\codenamephp\deployer\mariadb\dumpfile\factory\fromDatabaseNameAndTimestamp\SimpleNew::class, $this->sut->dumpfile);
    self::assertInstanceOf(\de\codenamephp\deployer\mariadb\task\dump\factory\SimpleNew::class, $this->sut->dump);
    self::assertInstanceOf(\de\codenamephp\deployer\mariadb\task\download\factory\SimpleNew::class, $this->sut->download);
    self::assertInstanceOf(\de\codenamephp\deployer\mariadb\task\deleteDump\factory\SimpleNew::class, $this->sut->deleteDump);
    self::assertInstanceOf(\de\codenamephp\deployer\mariadb\task\upload\factory\SimpleNew::class, $this->sut->upload);
    self::assertInstanceOf(\de\codenamephp\deployer\mariadb\task\import\factory\SimpleNew::class, $this->sut->import);
    self::assertSame($hostCheck, $this->sut->hostCheck);
  }

  public function test__invoke() : void {
    $this->sut->hostCheck = $this->createMock(iHostCheck::class);
    $this->sut->hostCheck->expects(self::once())->method('check');

    $sourceHost = $this->createMock(Host::class);
    $localhost = $this->createMock(Localhost::class);

    $this->sut->deployerFunctions = $this->createMock(iAll::class);
    $this->sut->deployerFunctions->expects(self::once())->method('getOption')->with(Copy::DB_SOURCE_HOST)->willReturn('some source host');
    $this->sut->deployerFunctions->expects(self::once())->method('localhost')->willReturn($localhost);
    $this->sut->deployerFunctions->expects(self::once())->method('host')->with('some source host')->willReturn($sourceHost);
    $this->sut->deployerFunctions->expects(self::exactly(2))
      ->method('on')
      ->withConsecutive([$sourceHost], [$localhost])
      ->willReturnCallback(static fn($hosts, Closure $callback) => $callback());

    $targetDatabase = $this->createMock(\de\codenamephp\deployer\mariadb\database\iDatabase::class);
    $sourceDatabase = $this->createMock(\de\codenamephp\deployer\mariadb\database\iDatabase::class);

    $this->sut->database = $this->createMock(iDatabase::class);
    $this->sut->database
      ->expects(self::exactly(2))
      ->method('fromConfigKey')
      ->willReturnOnConsecutiveCalls(
        $targetDatabase,
        $sourceDatabase
      );

    $dumpfile = $this->createMock(\de\codenamephp\deployer\mariadb\dumpfile\iDumpfile::class);

    $this->sut->dumpfile = $this->createMock(iDumpfile::class);
    $this->sut->dumpfile->expects(self::once())->method('create')->with($targetDatabase)->willReturn($dumpfile);

    $deleteDumpTask = $this->createMock(\de\codenamephp\deployer\mariadb\task\deleteDump\iDeleteDump::class);
    $deleteDumpTask->expects(self::exactly(3))->method('__invoke');

    $this->sut->deleteDump = $this->createMock(iDeleteDump::class);
    $this->sut->deleteDump->expects(self::once())->method('create')->with($dumpfile)->willReturn($deleteDumpTask);

    $dumpTask = $this->createMock(\de\codenamephp\deployer\mariadb\task\dump\iDump::class);
    $dumpTask->expects(self::once())->method('__invoke');

    $this->sut->dump = $this->createMock(iDump::class);
    $this->sut->dump->expects(self::once())->method('create')->with($sourceDatabase, $dumpfile)->willReturn($dumpTask);

    $downloadTask = $this->createMock(\de\codenamephp\deployer\mariadb\task\download\iDownload::class);
    $downloadTask->expects(self::once())->method('__invoke');

    $this->sut->download = $this->createMock(iDownload::class);
    $this->sut->download->expects(self::once())->method('createWithSingleDumpfile')->with($dumpfile)->willReturn($downloadTask);

    $uploadTask = $this->createMock(\de\codenamephp\deployer\mariadb\task\upload\iUpload::class);
    $uploadTask->expects(self::once())->method('__invoke');

    $this->sut->upload = $this->createMock(iUpload::class);
    $this->sut->upload->expects(self::once())->method('createWithSingleDumpfile')->with($dumpfile)->willReturn($uploadTask);

    $importTask = $this->createMock(\de\codenamephp\deployer\mariadb\task\import\iImport::class);
    $importTask->expects(self::once())->method('__invoke');

    $this->sut->import = $this->createMock(iImport::class);
    $this->sut->import->expects(self::once())->method('create')->with($targetDatabase, $dumpfile)->willReturn($importTask);

    $this->sut->__invoke();
  }

  public function testFindSourceHost_canThrowException_whenSourceHostOptionIsEmpty() : void {
    $this->expectException(MissingInputException::class);
    $this->expectExceptionMessage('The option for the source host must not be empty');

    $this->sut->deployerFunctions = $this->createMock(iAll::class);
    $this->sut->deployerFunctions->expects(self::once())->method('getOption')->with(Copy::DB_SOURCE_HOST)->willReturn(null);

    $this->sut->findSourceHost();
  }

  public function testFindSourceHost() : void {
    $host = $this->createMock(Host::class);

    $this->sut->deployerFunctions = $this->createMock(iAll::class);
    $this->sut->deployerFunctions->expects(self::once())->method('getOption')->with(Copy::DB_SOURCE_HOST)->willReturn('some host');
    $this->sut->deployerFunctions->expects(self::once())->method('host')->with('some host')->willReturn($host);

    self::assertSame($host, $this->sut->findSourceHost());
  }
}
