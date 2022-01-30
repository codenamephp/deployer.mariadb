<?php declare(strict_types=1);

namespace de\codenamephp\deployer\mariadb\test\dumpfile;

use DateTimeImmutable;
use de\codenamephp\deployer\mariadb\database\iDatabase;
use de\codenamephp\deployer\mariadb\database\Immutable;
use de\codenamephp\deployer\mariadb\dumpfile\FromDatabaseNameAndTimestamp;
use PHPUnit\Framework\TestCase;

final class FromDatabaseNameAndTimestampTest extends TestCase {

  private FromDatabaseNameAndTimestamp $sut;

  protected function setUp() : void {
    parent::setUp();

    $database = $this->createMock(iDatabase::class);

    $this->sut = new FromDatabaseNameAndTimestamp($database, '');
  }

  public function test__construct() : void {
    $this->sut = new FromDatabaseNameAndTimestamp(new Immutable('', '', 'mydb'), 'some/folder');

    self::assertStringContainsString('databasedump_mydb_', $this->sut->getName());
  }

  public function test__construct_withDateTime() : void {
    $this->sut = new FromDatabaseNameAndTimestamp(new Immutable('', '', 'mydb'), 'some/folder/', new DateTimeImmutable('2010-11-12 13:14:15'));

    self::assertEquals('some/folder/databasedump_mydb_20101112131415', (string) $this->sut);
  }
}
