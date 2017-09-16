<?php

namespace Greg\ToDo\Repositories;

use Greg\ToDo\Models\ModelInterface;

abstract class Repository
{
    const TABLE_NAME = "";
    const PRIMARY_KEY = "id";

    /** @var \PDO $connection */
    protected $connection;
    /** @var string $modelName */
    protected $modelName = "";
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
        if ($value instanceof ModelInterface) {
            $value = $value->primary();
        }

        $sql = 'SELECT * FROM '.static::TABLE_NAME.' WHERE '.static::PRIMARY_KEY.' = :value';
        $prepare = $this->connection->prepare($sql);
        $prepare->bindValue(':value', $value);
        $prepare->execute();
        $row = $prepare->fetch(\PDO::FETCH_ASSOC);
        if ($row === false) {
            return false;
        }
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
        if ($value instanceof ModelInterface) {
            $value = $value->primary();
        }

        $prepare = $this->connection->prepare('SELECT * FROM '.static::TABLE_NAME.' WHERE '.$key.' = :value');
        $prepare->bindValue(':value', $value);
        $prepare->execute();

        if ($single) {
            $row = $prepare->fetch(\PDO::FETCH_ASSOC);
            if ($row === false) {
                return false;
            }
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
        $prepare = $this->connection->query('SELECT * FROM '.static::TABLE_NAME);
        $data = $prepare->fetchAll(\PDO::FETCH_ASSOC);
        return $this->mapDataToModel($data);
    }

    /**
     * @param $model
     * @return bool|int
     * @throws \Exception
     * @deprecated
     */
    public function update($model)
    {
        $sql = 'UPDATE '.static::TABLE_NAME.' SET ';

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

        $sql .= ' WHERE '.static::PRIMARY_KEY.' = ?';

        $column_values[] = $this->columns[static::PRIMARY_KEY];

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
     * @param $model
     * @return bool|int
     * @throws \Exception
     * @deprecated
     */
    public function insert($model)
    {
        $sql = 'INSERT INTO '.static::TABLE_NAME.' ( ';
        $sql_questionmarks = '';
        $column_values = array();

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
     * @param array $data
     * @return ModelInterface[]
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
     * @param array $row
     * @return ModelInterface
     */
    private function mapRowToModel($row)
    {
        $model = new $this->modelName;
        foreach ($row as $columnName => $value) {
            $model->$columnName = $value;
        }
        return $model;
    }
}