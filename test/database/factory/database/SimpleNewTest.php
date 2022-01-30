<?php declare(strict_types=1);

namespace de\codenamephp\deployer\mariadb\test\database\factory\database;

use de\codenamephp\deployer\base\functions\All;
use de\codenamephp\deployer\base\functions\iGet;
use de\codenamephp\deployer\base\MissingConfigurationException;
use de\codenamephp\deployer\mariadb\database\factory\database\SimpleNew;
use de\codenamephp\deployer\mariadb\database\Immutable;
use de\codenamephp\deployer\mariadb\task\iTask;
use PHPUnit\Framework\TestCase;

final class SimpleNewTest extends TestCase {

  private SimpleNew $sut;

  protected function setUp() : void {
    parent::setUp();

    $this->sut = new SimpleNew();
  }

  public function test__construct() : void {
    self::assertInstanceOf(All::class, $this->sut->deployerFunctions);
  }

  public function testCreate() : void {
    self::assertEquals(
      new Immutable(
        'some user',
        'some password',
        'some host'),
      $this->sut->create(
        'some user',
        'some password',
        'some host'
      )
    );
    self::assertEquals(
      new Immutable(
        'some user',
        'some password',
        'some host',
        ['some', 'tables', '1234'],
        'some host',
        5678
      ),
      $this->sut->create(
        'some user',
        'some password',
        'some host',
        ['some', 'tables', 1234, null],
        'some host',
        5678
      )
    );
  }

  public function testCreate_canCastTablesToString() : void {
    self::assertSame(['some', '1234'], $this->sut->create('', '', '', ['some', 1234])->getTablesToIgnore());
  }

  public function testFromArray() : void {
    self::assertEquals(new Immutable('some user', 'some password', 'some name'), $this->sut->fromArray(['user' => 'some user', 'password' => 'some password', 'name' => 'some name']));
    self::assertEquals(
      new Immutable(
        'some user',
        'some password',
        'some name',
        ['some tables'],
        'some host',
        1234
      ),
      $this->sut->fromArray([
        'user' => 'some user',
        'password' => 'some password',
        'name' => 'some name',
        'tablesToIgnore' => ['some tables'],
        'host' => 'some host',
        'port' => '1234',
      ]));
    self::assertEquals(
      new Immutable(
        'some user',
        'some password',
        'some name',
        ['some tables'],
        'some host'
      ),
      $this->sut->fromArray([
        'user' => 'some user',
        'password' => 'some password',
        'name' => 'some name',
        'tablesToIgnore' => ['some tables'],
        'host' => 'some host',
      ]));

    self::assertSame(['some tables', '1234'], $this->sut->fromArray(['user' => '', 'password' => '', 'name' => '', 'tablesToIgnore' => ['some tables', 1234, null]])->getTablesToIgnore());
  }

  public function testFromArray_canCastValuesToString() : void {
    $database = $this->sut->fromArray(['user' => 123, 'password' => 456, 'name' => 789]);

    self::assertSame('123', $database->getUser());
    self::assertSame('456', $database->getPassword());
    self::assertSame('789', $database->getName());
  }

  public function testFromArray_canCastTablesToIgnoreToArray() : void {
    self::assertSame(['some table'], $this->sut->fromArray(['user' => '', 'password' => '', 'name' => '', 'tablesToIgnore' => 'some table'])->getTablesToIgnore());
  }

  public function testFromArray_canCastHostToString() : void {
    self::assertSame('1234', $this->sut->fromArray(['user' => '', 'password' => '', 'name' => '', 'host' => 1234])->getHost());
  }

  public function testFromArray_canThrowException_whenUsernameIsMissing() : void {
    $this->expectException(MissingConfigurationException::class);
    $this->expectExceptionMessage('User not set in database config');

    $this->sut->fromArray([]);
  }

  public function testFromArray_canThrowException_whenPasswordIsMissing() : void {
    $this->expectException(MissingConfigurationException::class);
    $this->expectExceptionMessage('Password not set in database config');

    $this->sut->fromArray(['user' => '']);
  }

  public function testFromArray_canThrowException_whenNameIsMissing() : void {
    $this->expectException(MissingConfigurationException::class);
    $this->expectExceptionMessage('Name not set in database config');

    $this->sut->fromArray(['user' => '', 'password' => '']);
  }

  public function testFindDatabase() : void {
    $this->sut->deployerFunctions = $this->createMock(iGet::class);
    $this->sut->deployerFunctions->expects(self::once())->method('get')->with('some key')->willReturn([
      'user' => 'some user',
      'password' => 'some password',
      'name' => 'some name',
    ]);

    self::assertEquals(new Immutable('some user', 'some password', 'some name'), $this->sut->fromConfigKey('some key'));
  }

  public function testFromConfigKey_canThrowException_ifConfigIsEmpty() : void {
    $this->expectException(MissingConfigurationException::class);
    $this->expectExceptionMessage('Database config was not set or empty');

    $this->sut->deployerFunctions = $this->createMock(iGet::class);
    $this->sut->deployerFunctions->expects(self::once())->method('get')->with(iTask::CONFIG_KEY_DATABASE)->willReturn(null);

    $this->sut->fromConfigKey();
  }
}
