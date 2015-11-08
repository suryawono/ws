<?php

class Model {

    var $db;
    var $belongsTo = [];
    var $hasMany = [];
    var $table;
    var $name;
    var $virtualFields = [];

    function __construct() {
        global $mysqli;
        $this->db = $mysqli;
        $this->name = get_class($this);
        $this->table = Inflector::tableize($this->name);
        foreach ($this->belongsTo as $model) {
            if (!@include_once( __FOLDER_MODEL . _DS_ . "{$model}.php" ))
                throw new Exception('Missing model ' . $model);
            $this->{$model} = new $model();
        }
        foreach ($this->hasMany as $model) {
            if (!@include_once( __FOLDER_MODEL . _DS_ . "{$model}.php" ))
                throw new Exception('Missing model ' . $model);
        }
    }

    function findById($id) {
        $id = is_null($id) ? "null" : $id;
        $virtualFieldString = $this->toVirtualFieldString();
        $result = $this->db->query(""
                . "select * "
                . "$virtualFieldString "
                . "from {$this->table} {$this->name} "
                . "where "
                . "{$this->name}.id={$id}");
        return [$this->name => buildResult($result->fetch_fields(), $result->fetch_row())];
    }

    function find($type = "all", $options = null) {
        $mydata;
        $r = [];
        $condString = "";
        $virtualFieldString = $this->toVirtualFieldString();
        if (isset($options['conditions'])) {
            $condString = $this->buildCond("or", $options['conditions']);
        }
        $condString = (empty($condString) ? "" : "where ") . $condString;
        if (!isset($options['fields'])) {
            $selector = "* $virtualFieldString";
        } else {
            $selector = $this->buildFields($options['fields']);
        }
        if (!isset($options["joins"]["left"])) {
            $leftjoin = "";
        } else {
            $leftjoin = $this->buildLeftjoin($options["joins"]["left"]);
        }
        if (!isset($options["group"])) {
            $groupby = "";
        } else {
            $groupby = $this->buildGroupBy($options["group"]);
        }
        switch ($type) {
            case "first":
                $result = $this->db->query(""
                        . "select $selector"
                        . "from {$this->table} {$this->name} "
                        . "$leftjoin "
                        . "$condString "
                        . "$groupby "
                        . "limit 1");
                $mydata = $r[$this->name] = buildResult($result->fetch_fields(), $result->fetch_row());
                if (empty($mydata['id'])) {
                    $mydata = $r = [];
                }
                break;
            case "all":
                $result = $this->db->query(""
                        . "select $selector "
                        . "from {$this->table} {$this->name} "
                        . "$leftjoin "
                        . "$condString "
                        . "$groupby ");
                $mydata = $r = buildResults($result, $this->name);
                break;
        }
        if (isset($options["contains"]) && !empty($mydata)) {
            $r = array_merge($r, $this->buildContain($options["contains"], $mydata));
        }
        return $r;
    }

    function toVirtualFieldString() {
        $virtualFieldString = ", ";
        foreach ($this->virtualFields as $k => $v) {
            $virtualFieldString.="(" . $v . ") '$k' , ";
        }
        $virtualFieldString = rtrim($virtualFieldString, ", ");
        return $virtualFieldString;
    }

    function buildCond($type = "or", $conds) {
        $ex = ["not", "and", "or"];
        if (strtolower($type) == "not") {
            $eq = "!=";
        } else {
            $eq = "=";
        }
        if (strtolower($type) == "and") {
            $glue = "and";
        } else {
            $glue = "or";
        }
        $condString = "";
        if (!empty($conds)) {
            $condString.="(";
            foreach ($conds as $k => $v) {
                if (in_array(strtolower($k), $ex)) {
                    $condString.=$this->buildCond($k, $v);
                } else {
                    $v = is_null($v) ? "null" : $v;
                    $condString.="$k $eq '$v' $glue ";
                }
            }
            $condString = rtrim($condString, "and ");
            $condString = rtrim($condString, "or ");
            $condString.=")";
            return $condString;
        } else {
            return "";
        }
    }

    function buildContain($contains, $reference_data) {
        $r = [];
        foreach ($contains as $k => $v) {
            if (is_numeric($k)) {
                if (array_search($v, $this->belongsTo) !== false) {
                    $r = array_merge($r, $this->$v->findById($reference_data[Inflector::underscore($v) . "_id"]));
                } else if (array_search($v, $this->hasMany) !== false) {
                    $m = new $v();
                    $entities = $m->find("all", [
                        "conditions" => [
                            Inflector::underscore($this->name) . "_id" => $reference_data['id']
                        ]
                    ]);
                    foreach ($entities as $entity) {
                        $r[$v][] = $entity[$v];
                    }
                }
            } else {
                if (array_search($k, $this->belongsTo) !== false) {
                    $current_data = $this->$k->findById($reference_data[Inflector::underscore($k) . "_id"]);
                    if (!empty($current_data)) {
                        $current_data[$k] += $this->$k->buildContain($v, $current_data[$k]);
                    }
                    $r = array_merge($r, $current_data);
                } else if (array_search($k, $this->hasMany) !== false) {
                    $m = new $k();
                    $entities = $m->find("all", [
                        "conditions" => [
                            Inflector::underscore($this->name) . "_id" => $reference_data['id']
                        ]
                    ]);
                    foreach ($entities as $entity) {
                        $eData = $entity[$k];
                        $m = new $k();
                        if (!empty($eData)) {
                            $eData+= $m->buildContain($v, $entity[$k]);
                        }
                        $r[$k][] = $eData;
                    }
                }
            }
        }
        return $r;
    }

    function buildFields($fields) {
        $fieldString = "";
        foreach ($fields as $field) {
            $fieldString.=$field;
            $fieldString.=" , ";
        }
        $fieldString = rtrim($fieldString, ", ");
        return $fieldString;
    }

    function buildLeftjoin($leftjoins) {
        $joinstring = "";
        foreach ($leftjoins as $leftjoin) {
            $joinstring.="left join " . Inflector::tableize($leftjoin) . " $leftjoin on $leftjoin.id={$this->name}." . Inflector::underscore($leftjoin) . "_id ";
        }
        return $joinstring;
    }

    function buildGroupBy($groups) {
        $groupstring = "group by ";
        foreach ($groups as $group) {
            $groupstring.=$group . ", ";
        }
        $groupstring = rtrim($groupstring, ", ");
        return $groupstring;
    }

    function needAuth() {
        
    }

}
