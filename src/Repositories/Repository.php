<?php

namespace Greg\ToDo\Repositories;

abstract class Repository
{
    /** @var \PDO $connection */
    protected $connection;
    /** @var string $modelName */
    protected $modelName = "";
    /** @var string $tableName */
    protected $tableName = "";
    /** @var string $primaryKey */
    protected $primaryKey = "id";

    /**
     * Repository constructor.
     * @param \PDO $connection
     */
    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function find($value)
    {
        $sql = 'SELECT * FROM '.$this->tableName.' WHERE '.$this->primaryKey.' = :value';
        $prepare = $this->connection->prepare($sql);
        $prepare->bindValue(':value', $value);
        $prepare->execute();
        $row = $prepare->fetch(\PDO::FETCH_ASSOC);
        return $this->mapRowToModel($row);
    }

    /**
     * @param $key string
     * @param $value mixed
     * @param $single boolean
     * @return array
     */
    public function findBy(string $key, $value, $single = false)
    {
        $prepare = $this->connection->prepare('SELECT * FROM '.$this->tableName.' WHERE '.$key.' = :value');
        $prepare->bindValue(':value', $value);
        $prepare->execute();
        if ($single) {
            $row = $prepare->fetch(\PDO::FETCH_ASSOC);
            return $this->mapRowToModel($row);
        }
        $data = $prepare->fetchAll(\PDO::FETCH_ASSOC);
        return $this->mapDataToModel($data);
    }

    /**
     * @return mixed
     */
    public function all()
    {
        $prepare = $this->connection->query('SELECT * FROM '.$this->tableName);
        $data = $prepare->fetchAll(\PDO::FETCH_ASSOC);
        return $this->mapDataToModel($data);
    }

    /**
     * @param $force_insert
     *
     * @return mixed
     */
    public function save($force_insert = false)
    {
        // lazy i know
        if ($force_insert) {
            return $this->insert();
        }
        if (!empty($this->columns[$this->primaryKey])) {
            // Primary key is set, use update
            return $this->update();
        }
        return $this->insert();
    }

    /**
     * @return bool
     * @throws \Exception
     */
    private function update()
    {
        $sql = 'UPDATE '.$this->tableName.' SET ';

        $column_values = array();

        $first = true;
        foreach ($this->columns as $key => $value) {
            // If this is NOT the first column, add a comma first
            if (!$first) {
                $sql .= ', ';
            }
            $first = false;

            $sql .= $key.' = ?';

            $column_values[] = $value;
        }

        $sql .= ' WHERE '.$this->primaryKey.' = ?';

        $column_values[] = $this->columns[$this->primaryKey];

        try {
            $prepare = $this->connection->prepare($sql);
            $prepare->execute($column_values);
            return $prepare->rowcount();
        } catch (\Exception $ex) {
            if (DEBUG) {
                throw $ex;
            }
            return false;
        }
        return false;
    }

    /**
     * @return mixed
     */
    private function insert($model)
    {
        // Create initial sql synthax
        $sql = 'INSERT INTO '.$this->tableName.' ( ';
        $sql_questionmarks = '';
        // Default is empty array
        $column_values = array();
        // Loop through column values
        $first = true;
        foreach ($this->columns as $key => $value) {
            // If this is NOT the first column, add a comma first
            if (!$first) {
                $sql .= ', ';
                $sql_questionmarks .= ', ';
            }
            $first = false;
            // Add the column name to the query
            $sql .= $key;
            // Add a question mark
            $sql_questionmarks .= ' ?';
            // Storre the value in the list
            $column_values[] = $value;
        }
        // Set the where clause using the primary
        $sql .= ') VALUES ('.$sql_questionmarks.')';
        try {
            // Prepare the statement
            $prepare = $this->connection->prepare($sql);
            // Attempt to execute the query
            $prepare->execute($column_values);
            // Return the resulting rowcount
            return $prepare->rowcount();
        } catch (\Exception $ex) {
            return false;
        }
        return false;
    }

    /**
     * @param $data
     * @return mixed
     */
    private function mapDataToModel($data)
    {
        $models = [];
        foreach ($data as $row) {
            $models[] = $this->mapRowToModel($row);
        }
        return $models;
    }

    /**
     * @param $row
     * @return mixed
     */
    private function mapRowToModel($row)
    {
        $model = new $this->modelName;
        foreach ($row as $columnName => $value) {
            $model->$columnName = $value;
        }
        return $model;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->columns[$key];
    }

    /**
     * Magic method to set all columns.
     *
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->columns[$key] = $value;
    }

}