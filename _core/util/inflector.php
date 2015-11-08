<?php
Class Inflector{
    
    static function modelate($controller){
        return rtrim($controller,"sController");
    }
    
    static function tableize($camelCase){
        return Inflector::pluralize(Inflector::underscore($camelCase));
    }
    
    static function underscore($camelCase){
        return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $camelCase));
    }
    
    static function pluralize($singular){
        if (substr($singular,-1)=="s"){
            return $singular."es";
        }
        return $singular."s";
    }
    
    static function singularize($plural){
        return rtrim($plural,"s");
    }
    
}