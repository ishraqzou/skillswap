<?php

namespace App\Core;

use PDO;

/**
 * Model - Classe abstraite de base pour tous les modèles.
 */
abstract class Model
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function find($id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function all(string $orderBy = ''): array
    {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy !== '') {
            $sql .= " ORDER BY {$orderBy}";
        }
        return $this->db->query($sql)->fetchAll();
    }

    public function where(array $conditions, string $orderBy = ''): array
    {
        [$clause, $params] = $this->buildWhere($conditions);
        $sql = "SELECT * FROM {$this->table} WHERE {$clause}";
        if ($orderBy !== '') {
            $sql .= " ORDER BY {$orderBy}";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function first(array $conditions): ?array
    {
        $rows = $this->where($conditions);
        return $rows[0] ?? null;
    }

    public function insert(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($c) => ':' . $c, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function update($id, array $data): bool
    {
        $assignments = implode(', ', array_map(fn($c) => "{$c} = :{$c}", array_keys($data)));
        $sql = "UPDATE {$this->table} SET {$assignments} WHERE {$this->primaryKey} = :__id";
        $data['__id'] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete($id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function count(array $conditions = []): int
    {
        if (empty($conditions)) {
            return (int) $this->db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
        }
        [$clause, $params] = $this->buildWhere($conditions);
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE {$clause}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    protected function query(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function buildWhere(array $conditions): array
    {
        $clauses = [];
        $params = [];
        foreach ($conditions as $column => $value) {
            $paramName = str_replace('.', '_', $column);
            $clauses[] = "{$column} = :{$paramName}";
            $params[$paramName] = $value;
        }
        return [implode(' AND ', $clauses), $params];
    }
}
