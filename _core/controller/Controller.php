<?php

class Controller {

    var $name;
    var $model_class;

    function __construct() {
        $this->name = get_class($this);
        $this->model_class = Inflector::modelate($this->name);
        if (!@include_once( __FOLDER_MODEL . _DS_ . "{$this->model_class}.php" ))
        throw new Exception('Missing model '.$this->model_class);
        $this->{$this->model_class}=new $this->model_class();
    }

    function needAuth() {
        
    }

}
