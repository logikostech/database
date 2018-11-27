<?php


namespace LogikosTest\Database;


use Logikos\Database\Connection;
use Logikos\Database\DbConfig;
use Logikos\Database\Exception;
use PHPUnit\Framework\TestCase;

class ConnectionTest extends TestCase {
  private $table;

  /** @var Connection */
  private $connection;

  /**
   * @throws Exception
   */
  public function setUp() {
    $this->connection = Connection::buildFromConfig(DbConfig::sqlite());
    $this->initRolesTable();
  }

  /** @throws Exception */
  public function testPdoQueryPrepareFailure() {
    $this->expectException(Exception::class);
    $this->connection->pdoQuery("select * from {$this->table} where foo=:foo", ['foo' => 'bar']);
  }

  /** @throws Exception */
  public function testPdoQueryExecuteFailure() {
    $this->expectException(Exception::class);
    $this->connection->pdoQuery("select * from {$this->table} where role=:foo", ['a' => 'bar']);
  }

  /** @throws Exception */
  public function testSqlite() {
    $this->selectFirstTest();
    $this->deleteTest();
  }

  /** @throws Exception */
  private function deleteTest() {
    $this->connection->deleteWhere($this->table, ['role'=>'admin']);
    $count = $this->connection->count($this->table, "role=:role", ['role'=>'admin']);

    $this->assertEquals(0, $count);
  }

  private function selectFirstTest() {
    $this->connection->insert(
        $this->table,
        ['role' => 'admin', 'description' => 'system administrator'],
        ['role' => 'users', 'description' => 'foo']
    );

    $row = $this->connection->selectFirst(
        $this->table,
        'role, description',
        ['role' => 'admin']
    );

    $this->assertEquals('admin', $row->role);
    $this->assertEquals('system administrator', $row->description);
  }

  /** @throws Exception */
  private function initRolesTable() {
    $this->table = 'roles';
    $fields = [
        'role VARCHAR(32)',
        'description VARCHAR(128)',
        'PRIMARY KEY(role)'
    ];
    $this->connection->createTable($this->table, ...$fields);
  }
}