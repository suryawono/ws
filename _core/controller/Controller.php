<?php

class Controller {

    var $db;

    function __construct() {
        global $mysqli;
        $this->db = $mysqli;
    }

    function needAuth() {
        
    }

}
