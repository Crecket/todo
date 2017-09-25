<?php

namespace Greg\ToDo\Repositories;

use Greg\ToDo\Database;
use Greg\ToDo\Exceptions\ORM\InvalidTargetModelGiven;
use Greg\ToDo\Exceptions\ORM\RelationNotFoundException;
use Greg\ToDo\Models\Model;

abstract class Repository
{
    const TABLE_NAME = "";

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
     * @return bool|Model
     */
    public function find($value)
    {
        if ($value instanceof Model) {
            $value = $value->primary();
        }

        $sql = "SELECT *, `primary_table`.`id` FROM `".static::TABLE_NAME."` as `primary_table`";

        // generate the relations sql
        $sqlRelations = $this->getRelationSql();

        // where statement to fetch the correct item
        $sqlWhere = " WHERE `primary_table`.`id` = :value";

        // execute the query
        $prepare = $this->database->connection->prepare($sql.$sqlRelations.$sqlWhere);
        $prepare->bindValue(":value", $value);
        $prepare->execute();
        $row = $prepare->fetch(\PDO::FETCH_ASSOC);

        if ($row === false) {
            return false;
        }

        return $this->mapRowToModel($row);
    }

    /**
     * @param string $column
     * @param $value
     * @param bool $single
     * @return null|Model|Model[]
     */
    public function findBy(string $column, $value, bool $single = false)
    {
        if ($value instanceof Model) {
            $value = $value->primary();
        }

        $sql = "SELECT *, `primary_table`.`id` FROM `".static::TABLE_NAME."` as `primary_table`";

        // generate the relations sql
        $sqlRelations = $this->getRelationSql();

        // where statement to fetch the correct item
        $sqlWhere = " WHERE `primary_table`.`".$column."` = :value";

        // execute the query
        $prepare = $this->database->connection->prepare($sql.$sqlRelations.$sqlWhere);
        $prepare->bindValue(":value", $value);
        $prepare->execute();

        if ($single) {
            $data = $prepare->fetch(\PDO::FETCH_ASSOC);
            return $this->mapRowToModel($data);
        }
        $data = $prepare->fetchAll(\PDO::FETCH_ASSOC);
        return $this->mapDataToModel($data);
    }

    /**
     * @return mixed
     */
    public function all()
    {
        $sql = "SELECT *, `primary_table`.`id` FROM `".static::TABLE_NAME."` as `primary_table`";
        $sql .= $this->getRelationSql();

        $query = $this->database->connection->query($sql);

        return $this->mapDataToModel(
            $query->fetchAll(\PDO::FETCH_ASSOC)
        );
    }

    /**
     * @param Model $model
     * @return int
     */
    public function delete(Model $model)
    {
        $sql = "DELETE FROM `".static::TABLE_NAME."` WHERE `id` = :value";

        $prepare = $this->database->connection->prepare($sql);
        $prepare->bindValue(":value", $model->primary());
        $prepare->execute();

        return $prepare->rowCount();
    }

    /**
     * @param Model $model
     * @return int
     */
    public function insert(Model $model)
    {
        $sql = "INSERT INTO ".static::TABLE_NAME." ( ";
        $sql_questionmarks = "";
        $column_values = array();

        // get all properties from the model
        $properties = get_object_vars($model);

        $first = true;
        foreach ($properties as $columnName => $columnValue) {
            if (is_null($columnValue)) {
                continue;
            }

            if (!$first) {
                $sql .= ", ";
                $sql_questionmarks .= ", ";
            }
            $first = false;

            $sql .= "`".$columnName."`";
            $sql_questionmarks .= " ?";

            $column_values[] = $columnValue;
        }
        $sql .= ") VALUES (".$sql_questionmarks.")";

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
        $sql = "UPDATE ".static::TABLE_NAME." SET ";
        $column_values = array();

        // get all properties from the model
        $properties = get_object_vars($model);
        $validColumns = $model->getColumns();

        $first = true;
        foreach ($properties as $columnName => $columnValue) {
            // check if this column is valid or added dynamically through relations
            if ($columnName === "id" || !in_array($columnName, $validColumns)) {
                continue;
            }

            if (!$first) {
                $sql .= ", ";
            }
            $first = false;

            $sql .= "`".$columnName."` = ?";

            $column_values[] = $columnValue;
        }

        $sql .= " WHERE `id` = ?";
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
        if (!isset($primaryRelations["has_many"][$targetModelName])) {
            throw new RelationNotFoundException();
        }

        // get the targetted column from the target model
        $targetColumn = $primaryRelations["has_many"][$targetModelName];

        // Select from current model and fetch any relations
        $sqlStart = "SELECT * FROM `".$targetModel::TABLE_NAME."` as `primary_table`";
        $sqlRelations = $this->getRelationSql($targetModelName);
        $sqlWhere = " WHERE `primary_table`.`".$targetColumn."` = ?";

        $sql = $sqlStart.$sqlRelations.$sqlWhere;
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
        if (!isset($primaryRelations["belongs_to"][$targetModelName])) {
            throw new RelationNotFoundException();
        }
        $targetRelations = $targetModel->getRelations();
        if (!isset($targetRelations["has_many"][get_class($primaryModel)])) {
            throw new RelationNotFoundException();
        }

        // get the targetted column from the target model
        $primaryColumn = $targetRelations["has_many"][get_class($primaryModel)];
        $targetColumn = $primaryRelations["belongs_to"][$targetModelName];

        // Select from current model and fetch any relations
        $sqlStart = "SELECT * FROM `".$targetModel::TABLE_NAME."` as `primary_table`";
        $sqlRelations = $this->getRelationSql($targetModelName);
        $sqlWhere = " WHERE `primary_table`.`".$targetColumn."` = ?";

        $sql = $sqlStart.$sqlRelations.$sqlWhere;
        $targetModels = $this->executeSql($sql, array($primaryModel->$primaryColumn), false);
        return $this->mapDataToModel($targetModels, $targetModelName);
    }

    /**
     * @param Model $primaryModel
     * @param string $targetModelName
     * @return Model
     * @throws InvalidTargetModelGiven
     * @throws RelationNotFoundException
     */
    public function hasOne(Model $primaryModel, string $targetModelName)
    {
        /** @var Model $targetModelInstance */
        $targetModel = new $targetModelName;
        if (!$targetModel instanceof Model) {
            throw new InvalidTargetModelGiven();
        }

        $primaryRelations = $primaryModel->getRelations();
        if (!isset($primaryRelations["has_one"][$targetModelName])) {
            throw new RelationNotFoundException();
        }

        // get the targetted column from the target model
        $primaryColumn = $primaryRelations["has_one"][$targetModelName];

        // Select from current model and join on $table_to_join
        $sqlStart = "SELECT * FROM `".$targetModel::TABLE_NAME."` as `primary_table`";
        $sqlRelations = $this->getRelationSql($targetModelName);
        $sqlWhere = " WHERE `primary_table`.`".$primaryColumn."` = ?";

        $sql = $sqlStart.$sqlRelations.$sqlWhere;
        $targetModel = $this->executeSql($sql, array($primaryModel->$primaryColumn));
        return $this->mapRowToModel($targetModel, $targetModelName);
    }

    /**
     * Generates required sql for 'hasOne' relations
     *
     * @param string $modelName
     * @param string $joinTable
     * @return string
     * @see https://github.com/Crecket/todo/issues/12
     */
    private function getRelationSql(string $modelName = null, string $joinTable = "primary_table")
    {
        if (is_null($modelName)) {
            $modelName = $this->modelName;
        }

        /** @var Model $model */
        $model = new $modelName;

        $relations = $model->getRelations();
        if (empty($relations["has_one"])) {
            return " ";
        }

        $relationSql = "";
        foreach ($relations["has_one"] as $targetRelation => $relationColumn) {
            /** @var Model $model */
            $targetModel = new $targetRelation;

            // create the inner join sql part
            $relationSql .= " INNER JOIN `".$targetModel::TABLE_NAME."` ON ".
                "`$joinTable`.`$relationColumn` = `".$targetModel::TABLE_NAME."`.`id`";

            // check for child relations
            $targetRelations = $targetModel->getRelations();
            if (!empty($targetRelations['has_one'])) {
                // target relations has child relations
                $relationSql .= $this->getRelationSql($targetRelation, $targetModel::TABLE_NAME);
            }
        }

        return $relationSql;
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
    private function mapRowToModel($row, string $modelName = null): Model
    {
        if ($modelName === null) {
            $modelName = $this->modelName;
        }

        $model = new $modelName;
        foreach ($row as $columnName => $value) {
            $model->$columnName = $value;
        }
        return $model;
    }
}