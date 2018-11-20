<?php


namespace LogikosTest\Database;

use Logikos\Database\BindsForInStatementFromArray;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Assert;

class BindsForInStatementFromArrayTest extends TestCase {
  public $origSql = 'IN(:placeholder)';
  public $name    = 'placeholder';

  public function testGetOriginalSql() {
    $sut = new BindsForInStatementFromArray($this->origSql, $this->name);
    Assert::assertSame($this->origSql, $sut->getOrigSql());
    Assert::assertSame($this->name, $sut->getName());
  }

  public function testBinds() {
    $data = ['a', 'b', 'c'];
    $sut = new BindsForInStatementFromArray($this->origSql, $this->name, $data);
    Assert::assertEquals(3, count($sut->binds()));
    Assert::assertEquals($data, array_values($sut->binds()));
    foreach($sut->binds() as $k=>$v) {
      Assert::assertContains($k, $sut->sql());
    }
    Assert::assertRegExp(
        "/IN\(\:placeholder[^,]+,\s?\:placeholder[^,]+,\s?\:placeholder[^,]+\)/",
        $sut->sql()
    );
  }

  public function test_WhenNameNotFoundAsPlaceholderInOrigSql_ExpectException() {
    $this->expectException(\Exception::class);
    new BindsForInStatementFromArray(
        'IN(:placeholder)',
        'foo'
    );
  }
}