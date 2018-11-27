<?php


namespace Logikos\Database;

use Nette\Database\ResultSet;
use PDOStatement;

class Connection extends \Nette\Database\Connection {

  public static function buildFromConfig(DbConfig $config, array $options = null) {
    return new static(
        $config->dsn(),
        $config->user,
        $config->pass,
        $options
    );
  }


  /**
   * @param  string $sql
   * @param  array  $binds
   * @return PDOStatement
   * @throws Exception
   */
  public function pdoQuery($sql, array $binds=[]) {
    $sth = $this->_prepare($sql);
    return $this->_execute($sth, $binds);
  }

  /**
   * @param $sql
   * @return PDOStatement
   * @throws Exception
   */
  private function _prepare($sql) {
    try {
      $sth = $this->getPdo()->prepare($sql);
      return $sth;
    }
    catch (\PDOException $e) {
      throw $this->prepareFailed($sql, $e);
    }
  }

  /**
   * @param PDOStatement $sth
   * @param array        $binds
   * @return PDOStatement
   * @throws Exception
   */
  private function _execute(PDOStatement $sth, array $binds=[]) {
    try {
      $this->_bindParams($sth, $binds);
      
      if ($sth->execute() === false)
        $this->executeFailed($sth, $binds);

      return $sth;
    }
    catch (\PDOException $e) {
      throw $this->executeFailed($sth, $binds, $e);
    }
  }

  private function executeFailed(PDOStatement $sth, array $binds, \PDOException $prev=null) {
    $sql = $sth->queryString;
    $bindParams = json_encode($binds);
    return new Exception(
        "Failed to execute {$sql} with binds {$bindParams}",
        0,
        $prev
    );
  }

  private function prepareFailed($sql, \PDOException $e) {
    return new Exception(
        "Failed to prepare \"{$sql}\"\n  Error: {$e->getMessage()}",
        0,
        $e
    );
  }

  private function _bindParams(PDOStatement $sth, $binds) {
    foreach ($binds as $name=>$value) {
      $sth->bindParam($this->bindParamName($name), $value);
    }
  }

  private function bindParamName($name) {
    return preg_match("/^:.+/", $name) ? $name : ":{$name}";
  }



  public function selectFirst($table, $itemString, array $where) {
    return $this->selectWhere($table, $itemString, $where)->fetch();
  }

  public function selectWhere($table, $itemString, array $where): ResultSet {
    return $this->query("SELECT {$itemString} FROM {$table} WHERE", $where);
  }

  public function insert($table, array ...$rows) {
    $this->query("INSERT INTO {$table}", $rows);
    return $this->getInsertId();
  }

  public function deleteWhere($table, array $where) {
    return $this->query("DELETE FROM {$table} WHERE ?", $where);
  }

  /**
   * @param       $table
   * @param       $whereString
   * @param array $binds
   * @return mixed
   * @throws Exception
   */
  public function count($table, $whereString, $binds=[]) {
    return $this->pdoQuery(
        "SELECT count(1) FROM {$table} WHERE {$whereString}",
        $binds
    )->fetchColumn();
  }

  /**
   * @param       $table
   * @param array ...$params
   * @throws Exception
   */
  public function createTable($table, ...$params) {
    $data = "\n  ".implode(",\n  ", $params)."\n";
    $sql = "CREATE TABLE {$table} ({$data})";
    $this->pdoQuery($sql);
  }
}