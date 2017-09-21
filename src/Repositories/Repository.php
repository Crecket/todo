<?php

namespace Greg\ToDo\Repositories;

use Greg\ToDo\Database;
use Greg\ToDo\Exceptions\ORM\InvalidTargetModelGiven;
use Greg\ToDo\Exceptions\ORM\RelationNotFoundException;
use Greg\ToDo\Models\Model;

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
        if ($value instanceof Model) {
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
        if ($value instanceof Model) {
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
     * @param Model $model
     * @return int
     */
    public function delete(Model $model)
    {
        $sql = 'DELETE FROM '.static::TABLE_NAME.' WHERE '.static::PRIMARY_KEY.' = :value';

        $prepare = $this->database->connection->prepare($sql);
        $prepare->bindValue(':value', $model->primary());
        $prepare->execute();

        return $prepare->rowCount();
    }

    /**
     * @param Model $model
     * @return int
     */
    public function insert(Model $model)
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
     * @param Model $model
     * @return int
     */
    public function update(Model $model)
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
     * @param Model $primaryModel
     * @param string $targetModelName
     * @return Model[]
     * @throws InvalidTargetModelGiven
     * @throws RelationNotFoundException
     */
    public function hasMany(Model $primaryModel, string $targetModelName)
    {
        /** @var Model $targetModelInstance */
        $targetModel = new $targetModelName;
        if (!$targetModel instanceof Model) {
            throw new InvalidTargetModelGiven();
        }

        $primaryRelations = $primaryModel->getRelations();
        if (!isset($primaryRelations['has_many'][$targetModelName])) {
            throw new RelationNotFoundException();
        }

        // get the targetted column from the target model
        $targetColumn = $primaryRelations['has_many'][$targetModelName];

        // Select from current model and join on $table_to_join
        $sql = 'SELECT * FROM `'.$targetModel::TABLE_NAME.'` as `t`'.'WHERE `t`.`'.$targetColumn.'` = ?';

        $targetModels = $this->executeSql($sql, array($primaryModel->primary()), false);
        return $this->mapDataToModel($targetModels, $targetModelName);
    }

    /**
     * @param Model $primaryModel
     * @param string $targetModelName
     * @return Model[]
     * @throws InvalidTargetModelGiven
     * @throws RelationNotFoundException
     */
    public function belongsTo(Model $primaryModel, string $targetModelName)
    {
        /** @var Model $targetModelInstance */
        $targetModel = new $targetModelName;
        if (!$targetModel instanceof Model) {
            throw new InvalidTargetModelGiven();
        }

        $primaryRelations = $primaryModel->getRelations();
        if (!isset($primaryRelations['belongs_to'][$targetModelName])) {
            throw new RelationNotFoundException();
        }
        $targetRelations = $targetModel->getRelations();
        if (!isset($targetRelations['has_many'][get_class($primaryModel)])) {
            throw new RelationNotFoundException();
        }

        // get the targetted column from the target model
        $primaryColumn = $targetRelations['has_many'][get_class($primaryModel)];
        $targetColumn = $primaryRelations['belongs_to'][$targetModelName];

        // Select from current model and join on $table_to_join
        $sql = 'SELECT * FROM `'.$targetModel::TABLE_NAME.'` as `t`'.' WHERE `t`.`'.$targetColumn.'` = ?';

        $targetModels = $this->executeSql($sql, array($primaryModel->$primaryColumn), false);
        return $this->mapDataToModel($targetModels, $targetModelName);
    }

    /**
     * @param string $sql
     * @param array $values
     * @param bool $single
     * @return array|mixed
     */
    private function executeSql(string $sql, array $values, bool $single = true)
    {
        $prepare = $this->database->connection->prepare($sql);
        $prepare->execute($values);
        if ($single) {
            return $prepare->fetch(\PDO::FETCH_ASSOC);
        }
        return $prepare->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param array $data
     * @param string|bool $modelName
     * @return Model[]
     */
    private function mapDataToModel(array $data, $modelName = false)
    {
        if ($modelName === false) {
            $modelName = $this->modelName;
        }

        $models = [];
        foreach ($data as $row) {
            $models[] = $this->mapRowToModel($row, $modelName);
        }
        return $models;
    }

    /**
     * @param array $row
     * @param string $modelName
     * @return Model
     */
    private function mapRowToModel($row, string $modelName): Model
    {
        $model = new $modelName;
        foreach ($row as $columnName => $value) {
            $model->$columnName = $value;
        }
        return $model;
    }
}