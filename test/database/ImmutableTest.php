<?php declare(strict_types=1);

namespace de\codenamephp\deployer\mariadb\test\database;

use de\codenamephp\deployer\mariadb\database\Immutable;
use PHPUnit\Framework\TestCase;

final class ImmutableTest extends TestCase {

  private Immutable $sut;

  protected function setUp() : void {
    parent::setUp();

    $this->sut = new Immutable('?', '?', '?');
  }

  public function test__construct() : void {
    $this->sut = new Immutable('some user', 'some password', 'some name');

    self::assertEquals('some user', $this->sut->getUser());
    self::assertEquals('some password', $this->sut->getPassword());
    self::assertEquals('some name', $this->sut->getName());
    self::assertEquals([], $this->sut->getTablesToIgnore());
    self::assertEquals('localhost', $this->sut->getHost());
    self::assertEquals(3306, $this->sut->getPort());
  }

  public function test__construct_withOptionalValues() : void {
    $this->sut = new Immutable('some user', 'some password', 'some name', ['some tables'], 'some host', 1234);

    self::assertEquals('some user', $this->sut->getUser());
    self::assertEquals('some password', $this->sut->getPassword());
    self::assertEquals('some name', $this->sut->getName());
    self::assertEquals(['some tables'], $this->sut->getTablesToIgnore());
    self::assertEquals('some host', $this->sut->getHost());
    self::assertEquals(1234, $this->sut->getPort());
  }
}
