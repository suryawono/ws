<?php

header('Content-Type: application/json');
require_once '/_core/bootstrap.php';

//open db
require_once __FOLDER_DB . _DS_ . "openDB.php";

//import plugin
require_once __FOLDER_PLUGIN . _DS_ . "basic.php";

//import base class
require_once __FOLDER_CONTROLLER . _DS_ . "Controller.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(generate_response(520));
    die;
}

//get controller and function
$excludeURI = trim(trim($_SERVER['PHP_SELF'], "index.php"), "/");

//any extension?
$uri = $_SERVER['REQUEST_URI'];
$anyExtension = explode(".", $uri);
if (count($anyExtension) > 1) {
    $uri = $anyExtension[0];
    $extension = $anyExtension[1];
}
$params = explode("/", trim(preg_replace("/\/" . $excludeURI . "/", "", $uri, 1), "/"));

if (!isset($params[0]) || !isset($params[1])) {
    echo json_encode(generate_response(501, null, array("controller" => @$params[0], "function" => @$params[1])));
    die;
}

$controllerName = camelize($params[0], true) . "Controller";

try {
    if (!@include_once( __FOLDER_CONTROLLER . _DS_ . "$controllerName.php" ))
        throw new Exception('Missing controller');
} catch (Exception $e) {
    echo json_encode(generate_response(510));
    die;
}

$controller = new $controllerName();

$functionName = $params[1];
try {
    if (!method_exists($controller, $functionName))
        throw new Exception('Missing function');
} catch (Exception $e) {
    echo json_encode(generate_response(511, null, array("controller" => $controllerName, "function" => $functionName)));
    die;
}

$controller->$functionName();
//echo json_encode(generate_response(200, null, $params));
