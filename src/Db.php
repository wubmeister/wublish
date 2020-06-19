<?php

namespace App;

use PDO;

/**
 * Class to interact with the database
 */
class Db
{
    const LIMIT_MAX = 18446744073709551615;

    /** @var PDO $pdo */
    private static $pdo;

    /**
     * Returns the shared PDO instance
     *
     * @return PDO
     */
    protected static function getPdo()
    {
        if (!self::$pdo) {
            $config = Config::get("Db");
            self::$pdo = new PDO("mysql:host={$config->host};dbname={$config->dbname}", $config->username, $config->password);
        }

        return self::$pdo;
    }

    /**
     * Prepares a statement
     *
     * @param string $sql
     * @param string $params Parameters to bind. Keys can be numeric or strings.
     *      String keys not starting with a colon will automatically be prefixed with a colon.
     * @return PDOStatement
     */
    protected static function prepare($sql, array $params = [])
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare($sql);
        if (!$stmt) {
            $err = $pdo->errorInfo();
            throw new DbException($err[2]);
        }

        foreach ($params as $key => $value) {
            if (is_numeric($key)) {
                $stmt->bindValue($key + 1, $value);
            } else {
                if ($key[0] != ':') $key = ":{$key}";
                $stmt->bindValue($key, $value);
            }
        }

        return $stmt;
    }

    /**
     * Perpares ans executes a statement
     *
     * @param string $sql
     * @param string $params Parameters to bind. Keys can be numeric or strings.
     *      String keys not starting with a colon will automatically be prefixed with a colon.
     * @return PDOStatement
     */
    public static function execute($sql, array $params = [])
    {
        $stmt = self::prepare($sql, $params);
        if (!$stmt->execute()) {
            $err = $stmt->errorInfo();
            throw new DbException($err[2]);
        }

        return $stmt;
    }

    /**
     * Fetches all records with the given query
     *
     * @param string $sql
     * @param string $params Parameters to bind. Keys can be numeric or strings.
     *      String keys not starting with a colon will automatically be prefixed with a colon.
     * @return array
     */
    public static function fetchAll($sql, array $params = [])
    {
        $stmt = self::execute($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetches one record with the given query
     *
     * @param string $sql
     * @param string $params Parameters to bind. Keys can be numeric or strings.
     *      String keys not starting with a colon will automatically be prefixed with a colon.
     * @return array|null If no record is found, this will return NULL
     */
    public static function fetchRow($sql, array $params = [])
    {
        $stmt = self::execute($sql, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Builds a SELECT SQL query
     *
     * @param string $table
     * @param string $where
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return string
     */
    protected static function makeSelect(string $table, $where = null, $order = null, $limit = null, $offset = null)
    {
        $sql = "SELECT * FROM `{$table}`";
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        if ($order) {
            $sql .= " ORDER BY {$order}";
        }
        if ($limit) {
            $limit = (int)$limit;
            if ($offset) {
                $offset = (int)$offset;
                $sql .= " LIMIT {$offset}, {$limit}";
            } else {
                $sql .= " LIMIT {$limit}";
            }
        } else if ($offset) {
            $offset = (int)$offset;
            $sql .= " LIMIT {$offset}, " . self::LIMIT_MAX;
        }

        return $sql;
    }

    /**
     * Finds all the records matching the given conditions
     *
     * @param string $table The table to search in
     * @param string $where The conditions as you would put them in a WHERE clause. Can contain parameter references (? and :param)
     * @param array $params Params to bind to the query, corresponding to the references in the $where parameter
     * @param string $order Order specification as you would put them in an ORDER clause
     * @param int $limit Maximum number of records to return
     * @param int $offset First record to return
     */
    public static function find(string $table, $where = null, $params = [], $order = null, $limit = null, $offset = null)
    {
        $sql = self::makeSelect($table, $where, $order, $limit, $offset);
        return self::fetchAll($sql, $params);
    }

    /**
     * Finds one record matching the given conditions
     *
     * @param string $table The table to search in
     * @param string $where The conditions as you would put them in a WHERE clause. Can contain parameter references (? and :param)
     * @param array $params Params to bind to the query, corresponding to the references in the $where parameter
     * @param string $order Order specification as you would put them in an ORDER clause
     * @param int $offset Offset of the record to return
     */
    public static function findRow(string $table, $where = null, $params = [], $order = null, $offset = null)
    {
        if (is_numeric($where)) {
            $params["id"] = $where;
            $where = "`id` = :id";
        }
        if ($offset) {
            $sql = self::makeSelect($table, $where, $order, 1, $offset);
        } else {
            $sql = self::makeSelect($table, $where, $order);
        }
        return self::fetchAll($sql, $params);
    }

    /**
     * Inserts a record in a table
     *
     * @param string $table The table to insert into
     * @param array $data The fields to insert
     */
    public static function insert(string $table, array $data)
    {
        $sql = "INSERT INTO `{$table}`
            (`" . implode("`, `", array_keys($data)) . "`)
            VALUES (:" . implode(", :", array_keys($data)) . ")";
        self::execute($sql, $data);
        return self::getPdo()->lastInsertId();
    }

    /**
     * Updates one or more records in a table
     *
     * @param string $table The table to update
     * @param array $data The fields to update
     * @param string $where The conditions as you would put them in a WHERE clause. Can contain parameter references (? and :param)
     * @param array $params Params to bind to the query, corresponding to the references in the $where parameter
     */
    public static function update(string $table, array $data, string $where = "", array $params = [])
    {
        $sets = [];
        $dataParams = [];
        foreach ($data as $key => $value) {
            $sets[] = "`{$key}` = :set_{$key}";
            $dataParams["set_{$key}"] = $value;
        }
        $sql = "UPDATE `{$table}` SET " . implode(", ", $sets);
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        $params = array_merge($dataParams, $params);
        $stmt = self::execute($sql, $params);

        return $stmt->rowCount();
    }

    /**
     * Deletes one ore more records from a table
     *
     * @param string $table The table to delete from
     * @param string $where The conditions as you would put them in a WHERE clause. Can contain parameter references (? and :param)
     * @param array $params Params to bind to the query, corresponding to the references in the $where parameter
     */
    public static function delete(string $table, string $where = "", array $params = [])
    {
        $sql = "DELETE FROM `{$table}`";
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        $stmt = self::execute($sql, $params);

        return $stmt->rowCount();
    }
}
