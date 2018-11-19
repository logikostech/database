<?php


namespace LogikosTest\Database;

use PHPUnit\Framework\TestCase;

class BindsForInStatementFromArrayTest extends TestCase {
  public $origSql = 'IN(:placeholder)';
  public $name    = 'placeholder';

  public function testGetOriginalSql() {
    $sut = new BindsForInStatementFromArray($this->origSql, $this->name);
    $this->assertSame($this->origSql, $sut->getOrigSql());
    $this->assertSame($this->name, $sut->getName());
  }

  public function testBinds() {
    $this->markTestSkipped();
    $data = ['a', 'b', 'c'];
    $sut = new BindsForInStatementFromArray($this->origSql, $this->name, $data);
    var_dump($sut->sql(), $sut->binds());
  }
}

class BindsForInStatementFromArray {
  private $origSql;
  private $name;
  private $data = [];
  private $placeholders = [];
  private $binds = [];
  private $sql;

  public function __construct($origSql, $name, $data = []) {
    $this->origSql = $origSql;
    $this->name = $name;
    $this->data = $data;
    $this->process();
  }

  public function getOrigSql() { return $this->origSql; }
  public function getName()    { return $this->name;    }
  public function binds()      { return $this->binds;   }
  public function sql()        { return $this->sql;     }

  private function process() {
    foreach ($this->data as $value)
      $this->processBind($value);

    $this->buildSql();
  }

  private function processBind($value) {
    $key = $this->genKey();
    $this->binds[$key] = $value;
    array_push($this->placeholders, $key);
  }

  private function genKey() {
    static $i = 0;
    return uniqid($this->placeholderPrefix($i));
  }

  private function buildSql() {
    $this->sql = str_replace(
        ":{$this->name}",
        $this->placeholderString(),
        $this->origSql
    );
  }

  private function placeholderString(): string {
    return ':' . implode(', :', $this->placeholders);
  }

  private function placeholderPrefix($i): string {
    return $this->name . $i++ . '_';
  }
}