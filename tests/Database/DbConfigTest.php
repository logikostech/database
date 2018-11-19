<?php

namespace LogikosTest\Database;

use Logikos\Database\DbConfig;
use PHPUnit\Framework\TestCase;

class DbConfigTest extends TestCase {

  public function testSqlite() {
    $c = DbConfig::sqlite();
    $this->assertTrue($c->isSqlite());
    $this->assertEquals('sqlite', $c->driver);
    $this->assertEquals(':memory:', $c->location);
    $this->assertTrue($c->isValid());
  }

  public function testMysql() {
    $c = new DbConfig();
    $this->assertFalse($c->isSqlite());
  }

  public function testDsn() {
    $this->markTestSkipped();
  }

  public function testIsSqlite() {
    $this->markTestSkipped();
  }
}
