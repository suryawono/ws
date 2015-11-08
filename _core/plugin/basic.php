<?php

$codeList = array(
    101 => "Validasi error",
    200 => "Berhasil disimpan",
    201 => "Berhasil dihapus",
    202 => "Login berhasil",
    204 => "Data berhasil dihapus",
    205 => "Status berubah",
    400 => "Data found",
    401 => "Data not found",
    402 => "Login gagal",
    403 => "Invalid request",
    404 => "Akun belum aktif",
    501 => "Invalid controller and function",
    510 => "Controller not found",
    511 => "Function not found",
    520 => "Wrong method",
);

function generate_response($code, $message = null, $data = array()) {
    global $codeList;
    if (is_null($message)) {
        $message = $codeList[$code];
    }
    return array(
        "response" => array(
            "status" => $code,
            "message" => $message,
            "data" => $data,
        )
    );
}

function camelize($string, $capitalizeFirstCharacter = false) {
    $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));

    if (!$capitalizeFirstCharacter) {
        $str[0] = strtolower($str[0]);
    }

    return $str;
}

function buildResult($fieldsName, $values) {
    $result = array();
    foreach ($fieldsName as $k => $v) {
        $result[$v->name] = $values[$k];
    }
    return $result;
}

function buildResults($results,$modelname) {
    $result = array();
    $fieldsName = $results->fetch_fields();
    while ($values = $results->fetch_row()) {
        $r=[];
        foreach ($fieldsName as $k => $v) {
            $r[$v->name] = $values[$k];
        }
        $result[][$modelname]=$r;
    }
    return $result;
}
