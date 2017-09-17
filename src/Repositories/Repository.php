<?php

namespace Greg\ToDo\Repositories;

use Greg\ToDo\Database;
use Greg\ToDo\Models\ModelInterface;

abstract class Repository
{
    const TABLE_NAME = "";
    const PRIMARY_KEY = "id";

    /** @var Database $database */
    protected $database;
    /** @var string $modelName */
    protected $modelName = "";

    /**
     * Repository constructor.
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
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

        $prepare = $this->database->connection->prepare($sql);
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

        $prepare = $this->database->connection->prepare('SELECT * FROM '.static::TABLE_NAME.' WHERE '.$key.' = :value');
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
        $prepare = $this->database->connection->query('SELECT * FROM '.static::TABLE_NAME);
        $data = $prepare->fetchAll(\PDO::FETCH_ASSOC);
        return $this->mapDataToModel($data);
    }

    /**
     * @param ModelInterface $model
     * @return int
     */
    public function delete(ModelInterface $model)
    {
        $sql = 'DELETE FROM '.static::TABLE_NAME.' WHERE '.static::PRIMARY_KEY.' = :value';

        $prepare = $this->database->connection->prepare($sql);
        $prepare->bindValue(':value', $model->primary());
        $prepare->execute();

        return $prepare->rowCount();
    }

    /**
     * @param ModelInterface $model
     * @return int
     */
    public function insert(ModelInterface $model)
    {
        $sql = 'INSERT INTO '.static::TABLE_NAME.' ( ';
        $sql_questionmarks = '';
        $column_values = array();

        // get all properties from the model
        $properties = get_object_vars($model);

        $first = true;
        foreach ($properties as $columnName => $columnValue) {
            if (is_null($columnValue)) {
                continue;
            }

            if (!$first) {
                $sql .= ', ';
                $sql_questionmarks .= ', ';
            }
            $first = false;

            $sql .= "`".$columnName."`";
            $sql_questionmarks .= ' ?';

            $column_values[] = $columnValue;
        }
        $sql .= ') VALUES ('.$sql_questionmarks.')';

        $prepare = $this->database->connection->prepare($sql);
        $prepare->execute($column_values);
        return $prepare->rowcount();
    }

    /**
     * @param ModelInterface $model
     * @return int
     */
    public function update(ModelInterface $model)
    {
        $sql = 'UPDATE '.static::TABLE_NAME.' SET ';
        $column_values = array();

        // get all properties from the model
        $properties = get_object_vars($model);

        $first = true;
        foreach ($properties as $columnName => $columnValue) {
            if ($columnName === static::PRIMARY_KEY) {
                continue;
            }

            if (!$first) {
                $sql .= ', ';
            }
            $first = false;

            $sql .= "`".$columnName.'` = ?';

            $column_values[] = $columnValue;
        }

        $sql .= ' WHERE `'.static::PRIMARY_KEY.'` = ?';
        $column_values[] = $model->primary();

        $prepare = $this->database->connection->prepare($sql);
        $prepare->execute($column_values);
        return $prepare->rowcount();
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