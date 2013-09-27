<?php

namespace Octopod\Octophp;


abstract class BaseModel {

    // Override this property
    protected static $table = null;
    protected $data = null;
    protected $enteredFields = null;
    protected static $fields = null;

    protected static $fieldTypes = array("string" => "TEXT", "int" => "INTEGER");

    function __construct($dataArray)
    {
        foreach (static::$fields as $key => $field) {
            if (isset ($dataArray[$key]) && !is_null($dataArray[$key])) #TODO Провалидировать условие
            {
                $this->data[$key] = $dataArray[$key];
                $this->enteredFields[$key] = true;
            } elseif ($field["required"]) {
                print "Error! Requered parameter '" . $key . "' missing in constructor!\n"; #TODO Переделать на эксепшен
            }
        }

        foreach ($dataArray as $key => $value) {
            if (!$this->enteredFields[$key]) {
                print "Warning! Received parameter '" . $key . "' left unused!\n"; #TODO Переделать на эксепшен
            }
        }
    }

    public static function getQueryCreate()
    {

        $query = "CREATE TABLE IF NOT EXISTS '" . static::$table . "' (";
        foreach (static::$fields as $key => $field) {
            $fieldDesc = $key . " ";
            switch ($field["type"]) {
                case "key":
                    $fieldDesc .= "INTEGER PRIMARY KEY";
                    break;
                case "column" :
                    $fieldDesc .= static::$fieldTypes[$field["datatype"]];
                    break;
            }
            $fields[] = $fieldDesc;
        }

        $query .= implode(',', $fields) . ");\n";

        return $query;
    }

    public static function getQueryDrop()
    {

        $query = " DROP TABLE IF EXISTS '" . static::$table . "';\n";
        return $query;
    }


    /**
     * Generates insert query
     *
     * @return string
     */
    public function getQueryInsert()
    {

        // @todo: secure from SQL injection

        $query = "INSERT INTO '" . static::$table . "'";

        $columns_portion = ' (' . implode(',', array_map(function ($columnName) { return "`$columnName`"; }, $this->getFieldsList())) . ')';

        $query .= $columns_portion;
        $query .= ' VALUES';

        $values = array();
        foreach (static::$fields as $key => $field) {
            if (!is_null($this->data[$key]) || $field['type'] != 'key') {
                $values[] = "'" . $this->data[$key] . "'";
            }
        }

        $values_portion = ' (' . implode(',', $values) . ')';
        $query .= $values_portion;

        $query .= ";\n";
        return $query;
    }

    protected function getFieldsList()
    {
        foreach (static::$fields as $key => $field) {

            if (!is_null($this->data[$key]) || $field['type'] != 'key') {
                $result[] = $key;
            }
        }
        return $result;

    }

    //    public function getQueryInsert() {
    //
    //        // @todo: secure from SQL injection
    //
    //        $query = "INSERT INTO '{$this->table}'";
    //
    //        $columns = $this::columns();
    //        $columns_portion = ' ('.implode(',', array_map(function($column){ return "\"$column\""; }, $columns)).')';
    //
    //        $query .= $columns_portion;
    //        $query .= ' VALUES';
    //
    //        $values = array();
    //        foreach ( $columns as $column ) {
    //            $value = $this->$column;
    //            $value = "'$value'";
    //            $values[] = $value;
    //        }
    //
    //        $values_portion = ' ('.implode(',', $values).')';
    //        $query .= $values_portion;
    //
    //        return $query;
    //    }

    public function __set($key, $value)
    {
        $setter = 'set' . ucfirst($key);
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } else if (property_exists($this, $key)) {
            $this->$key = $value;
        }
    }

    public function __get($key)
    {
        $getter = 'get' . ucfirst($key);
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
        if (property_exists($this, $key)) {
            return $this->$key;
        }

        return null;
    }

}