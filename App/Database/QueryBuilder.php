<?php

namespace App\Database;
use PDO;

use App\Database\Connection as Connection;

class QueryBuilder {
    protected $table;
    protected $select = '*';
    protected $joins = [];
    protected $where = [];
    protected $orderBy;
    protected $limit;
    protected $insertValues = [];
    protected $updateValues = [];
    protected $deleteFlag = false;

    protected $fetchAll = false;

    protected $getQuery = false;

    protected $pdo;

    public function __construct()
    {
        $database = new Connection();
        $this->pdo = $database->getPdo();
    }

    public function table($table) {
        $this->table = $table;
        return $this;
    }

    public function select($columns) {
        $this->select = is_array($columns) ? implode(', ', $columns) : $columns;
        return $this;
    }

    public function join($table, $first, $operator, $second, $type = 'INNER') {
        $this->joins[] = "$type JOIN $table ON $first $operator $second";
        return $this;
    }

    public function where($column, $operator, $value) {
        $this->where[] = "$column $operator '$value'";
        return $this;
    }

    public function orderBy($column, $direction = 'ASC') {
        $this->orderBy = "$column $direction";
        return $this;
    }

    public function limit($limit) {
        $this->limit = $limit;
        return $this;
    }

    public function insert($values) {
        $this->insertValues = $values;
        return $this;
    }

    public function update($values) {
        $this->updateValues = $values;
        return $this;
    }

    public function delete() {
        $this->deleteFlag = true;
        return $this;
    }

    public function getAll()
    {
        $this->fetchAll = true;
        $this->getQuery = true;
        return $this;
    }

    public function get()
    {
        $this->fetchAll = false;
        $this->getQuery = true;
        return $this;
    }

    public function execute() {
        $sql = '';

        if ($this->insertValues) {
            $sql = "INSERT INTO $this->table (" . implode(', ', array_keys($this->insertValues)) . ") VALUES ('" . implode("', '", array_values($this->insertValues)) . "')";
            $statement = $this->pdo->prepare($sql);
            $success = $statement->execute();
            if($success) return true;
            else return false;
        } elseif ($this->updateValues) {
            $setValues = [];
            foreach ($this->updateValues as $column => $value) {
                $setValues[] = "$column = '$value'";
            }
            $sql = "UPDATE $this->table SET " . implode(', ', $setValues);
            if (!empty($this->where)) {
                $sql .= ' WHERE ' . implode(' AND ', $this->where);
            }

            $statement = $this->pdo->prepare($sql);
            $success = $statement->execute();
            if($success) return true;
            else return false;
        } elseif ($this->deleteFlag) {
            $sql = "DELETE FROM $this->table";
            if (!empty($this->where)) {
                $sql .= ' WHERE ' . implode(' AND ', $this->where);
            }

            $statement = $this->pdo->prepare($sql);
            $success = $statement->execute();
            if($success) return true;
            else return false;
        } else {
            $sql = "SELECT $this->select FROM $this->table";

            foreach ($this->joins as $join) {
                $sql .= " $join";
            }

            if (!empty($this->where)) {
                $sql .= ' WHERE ' . implode(' AND ', $this->where);
            }

            if ($this->orderBy) {
                $sql .= " ORDER BY $this->orderBy";
            }

            if ($this->limit) {
                $sql .= " LIMIT $this->limit";
            }

            if($this->getQuery){
                $statement = $this->pdo->prepare($sql);
                $success = $statement->execute();
                if (!$success) {
                    $errorInfo = $statement->errorInfo();
                    return "Error : " . $errorInfo[2]; // Returning the error message
                }
                if ($this->fetchAll) {
                    return $statement->fetchAll(PDO::FETCH_ASSOC);
                }
                else {
                    return $statement->fetch(PDO::FETCH_ASSOC);
                }
            }
        }
    }
}
