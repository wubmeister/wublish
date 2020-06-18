<?php

namespace App;

use PDO;

/**
 * Class to interact with the database
 */
class Db
{
    const LIMIT_MAX = 18446744073709551615;

    protected static function getPdo()
    {
        if (!self::$pdo) {
            $config = Config::get("Db");
            self::$pdo = new PDO("mysql:host={$config->host},dbname={$config->dbname}", $config->username, $config->password);
        }

        return self::$pdo;
    }

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

    public static function execute($sql, array $params = [])
    {
        $stmt = self::prepare($sql, $params);
        if (!$stmt->exec()) {
            $err = $stmt->errorInfo();
            throw new DbException($err[2]);
        }

        return $stmt;
    }

    public static function fetchAll($sql, array $params = [])
    {
        $stmt = self::execute($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function fetchRow($sql, array $params = [])
    {
        $stmt = self::execute($sql, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

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

    public static function find(string $table, $where = null, $params = [], $order = null, $limit = null, $offset = null)
    {
        $sql = self::makeSelect($table, $where, $order, $limit, $offset);
        return self::fetchAll($sql, $params);
    }

    public static function findRow(string $table, $where = null, $params = [], $order = null, $limit = null, $offset = null)
    {
        if (is_numeric($where)) {
            $params["id"] = $where;
            $where = "`id` = :id";
        }
        $sql = self::makeSelect($table, $where, $order, $limit, $offset);
        return self::fetchAll($sql, $params);
    }

    public static function insert(string $table, array $data)
    {
        $sql = "INSERT INTO `{$table}`
            (`" . implode("`, `", array_keys($data)) . "`)
            VALUES (:" . implode(", :", array_keys($data)) . ")";
        self::execute($sql, $data);
        return self::getPdo()->lastInsertId();
    }

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
