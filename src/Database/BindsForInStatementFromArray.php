<?php

namespace Logikos\Database;


use Logikos\Util\Validation\Validator\Exception;

class BindsForInStatementFromArray {
  private $origSql;
  private $name;
  private $data         = [];
  private $placeholders = [];
  private $binds        = [];
  private $sql;
  private static $i = 0;

  public function __construct($origSql, $name, $data = []) {
    $this->origSql = $origSql;
    $this->name = $name;
    $this->data = $data;
    $this->validate();
    $this->process();
  }

  public function getOrigSql() { return $this->origSql; }
  public function getName()    { return $this->name;    }
  public function binds()      { return $this->binds;   }
  public function sql()        { return $this->sql;     }

  private function validate() {
    if (!strstr($this->origSql, $this->name))
      throw new Exception("could not find the string ':{$this->name}' in the original sql you provided.");
  }

  private function process() {
    foreach ($this->data as $value)
      $this->processBind($value);

    $this->buildSql();
  }

  private function processBind($value) {
    $key = $this->nextKey();
    $this->binds[$key] = $value;
    array_push($this->placeholders, $key);
  }

  private function nextKey() {
    return sprintf(
        "%s_%s",
        $this->name,
        self::$i++
    );

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
}