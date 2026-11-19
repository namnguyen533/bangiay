<?php

namespace App\Core;

use PDO;
use PDOStatement;

abstract class BaseModel
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetch(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params)->fetch();
        return $result ?: null;
    }

    public function fetchColumn(string $sql, array $params = [])
    {
        return $this->query($sql, $params)->fetchColumn();
    }

    public function insert(string $table, array $data): int
    {
        $fields = array_keys($data);
        $columns = implode(', ', $fields);
        $placeholders = ':' . implode(', :', $fields);

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, $data);

        return (int)$this->db->lastInsertId();
    }

    public function update(string $table, array $data, string $where, array $whereParams = []): bool
    {
        $set = [];
        $params = [];
        foreach ($data as $column => $value) {
            $set[] = "{$column} = :set_{$column}";
            $params["set_{$column}"] = $value;
        }

        $setString = implode(', ', $set);
        $sql = "UPDATE {$table} SET {$setString} WHERE {$where}";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(array_merge($params, $whereParams));
    }

    public function delete(string $table, string $where, array $whereParams = []): bool
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($whereParams);
    }

    public function beginTransaction(): void
    {
        $this->db->beginTransaction();
    }

    public function commit(): void
    {
        $this->db->commit();
    }

    public function rollBack(): void
    {
        $this->db->rollBack();
    }

    public function inTransaction(): bool
    {
        return $this->db->inTransaction();
    }
}
