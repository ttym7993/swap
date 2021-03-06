<?php
/**
 * SQLite 关系数据库连接和结果集抽象
 *
 * @copyright Copyright (c) 2009-2015 Jingcheng Zhang <diogin@gmail.com>. All rights reserved.
 * @license   See "LICENSE" file bundled with this distribution.
 */
namespace kern;
use SQLite3;
use Exception;
if (!extension_loaded('SQLite3')) throw new environment_error('cannot use sqlite_rdb_conn: sqlite3 extension does not exist');
// [类型] sqlite 关系数据库节点
abstract class sqlite_rdb_node extends rdb_node {}
// [类型] sqlite 关系数据库主节点
class sqlite_master_rdb_node extends sqlite_rdb_node {}
// [类型] sqlite 关系数据库从节点
class sqlite_slave_rdb_node extends sqlite_rdb_node {}
// [类型] sqlite 关系数据库连接
class /* @kern */ sqlite_rdb_conn extends rdb_conn {
    public function __construct($dsn) {
        try {
            list(, $db_file) = explode('://', $dsn, 2);
            $conn = new SQLite3($db_file);
            $this->conn = $conn;
        } catch (Exception $e) {
            throw new server_except("cannot connect to database: {$dsn}");
        }
    }
    public function select($sql) {
        $result = $this->conn->query($sql);
        if ($result === false) {
            return false;
        }
        return new sqlite_rdb_result($result);
    }
    public function execute($sql) {
        return $this->conn->exec($sql);
    }
    public function insert_id() {
        return $this->conn->lastInsertRowID();
    }
    public function affected_rows() {
        return $this->conn->changes();
    }
    public function begin() {
    }
    public function commit() {
    }
    public function rollback() {
    }
    public function last_error() {
        return $this->conn->lastErrorMsg();
    }
    public function escape($value) {
        return SQLite3::escapeString($value);
    }
    public function get_limit_sql($page_size, $begin_offset) {
        return "LIMIT {$page_size} OFFSET {$begin_offset}";
    }
    public function build_table_name($table_name) {
        return '`' . $table_name . '`';
    }
    public function build_field_name($field_name) {
        return '`' . $field_name . '`';
    }
    protected $conn = null;
}
// [类型] sqlite 关系数据库结果集
class /* @kern */ sqlite_rdb_result extends rdb_result {
    public function __construct($result) {
        $this->result = $result;
    }
    public function fetch_record() {
        $record = $this->result->fetchArray(SQLITE3_ASSOC);
        if ($record === false) {
            return null;
        }
        // @todo: 转成 php 的类型
        return $record;
    }
    public function num_rows() {
        $num_rows = 0;
        $this->result->reset();
        while ($this->result->fetchArray(SQLITE3_ASSOC) !== false) {
            $num_rows++;
        }
        $this->result->reset();
        return $num_rows;
    }
    public function free() {
        $this->result->finalize();
    }
    protected $result = null;
}
