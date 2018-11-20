<?php

namespace LogikosTest\Database;

use Logikos\Database\DbConfig;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Assert;

class DbConfigTest extends TestCase {

  public function testSqliteDefaults() {
    $c = DbConfig::sqlite();
    Assert::assertTrue($c->isSqlite());
    Assert::assertEquals('sqlite', $c->driver);
    Assert::assertEquals(':memory:', $c->location);
    Assert::assertEquals('sqlite::memory:',$c->dsn());
    Assert::assertTrue($c->isValid());
  }

  public function testSqlite() {
    $c = DbConfig::sqlite('loc');
    Assert::assertTrue($c->isSqlite());
    Assert::assertEquals('sqlite', $c->driver);
    Assert::assertEquals('loc', $c->location);
    Assert::assertEquals('sqlite:loc',$c->dsn());
    Assert::assertTrue($c->isValid());
  }

  public function testMysql() {
    $c = DbConfig::mysql('h','n','u','p',123);
    Assert::assertEquals('h', $c->host);
    Assert::assertEquals('n', $c->name);
    Assert::assertEquals('u', $c->user);
    Assert::assertEquals('p', $c->pass);
    Assert::assertEquals(123, $c->port);
    Assert::assertEquals('mysql', $c->driver);
    Assert::assertEquals('mysql:host=h;port=123;dbname=n',$c->dsn());
    Assert::assertFalse($c->isSqlite());
  }

  public function testDsn() {
    $this->markTestSkipped();
  }

  public function testIsSqlite() {
    $this->markTestSkipped();

  }
}
