<?php declare(strict_types=1);

namespace de\codenamephp\deployer\mariadb\test\task\import;

use de\codenamephp\deployer\base\functions\All;
use de\codenamephp\deployer\base\functions\iRun;
use de\codenamephp\deployer\mariadb\database\iDatabase;
use de\codenamephp\deployer\mariadb\database\Immutable;
use de\codenamephp\deployer\mariadb\dumpfile\iDumpfile;
use de\codenamephp\deployer\mariadb\task\import\Import;
use PHPUnit\Framework\TestCase;

final class ImportTest extends TestCase {

  private Import $sut;

  protected function setUp() : void {
    parent::setUp();

    $database = $this->createMock(iDatabase::class);
    $dumpfile = $this->createMock(iDumpfile::class);

    $this->sut = new Import($database, $dumpfile);
  }

  public function test__invoke() : void {
    $this->sut->database = new Immutable('myUser', 'topSecret', 'myDatabase', [], 'myHost', 1234);
    $this->sut->dumpfile = $this->createConfiguredMock(iDumpfile::class, ['getFilename' => '/some/folder/file']);

    $this->sut->deployerFunctions = $this->createMock(iRun::class);
    $this->sut->deployerFunctions
      ->expects(self::exactly(2))
      ->method('run')
      ->withConsecutive(
        ['mysql --user=myUser --password=topSecret --host=myHost --port=1234 -e "CREATE DATABASE IF NOT EXISTS myDatabase;"'],
        ['mysql --user=myUser --password=topSecret --host=myHost --port=1234 myDatabase < /some/folder/file;']
      );

    $this->sut->__invoke();
  }

  public function test__construct() : void {
    $database = $this->createMock(iDatabase::class);
    $dumpfile = $this->createMock(iDumpfile::class);

    $this->sut = new Import($database, $dumpfile);

    self::assertSame($database, $this->sut->database);
    self::assertSame($dumpfile, $this->sut->dumpfile);
    self::assertInstanceOf(All::class, $this->sut->deployerFunctions);
  }
}
