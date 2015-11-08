<?php

class JenisAnggotasController extends Controller {

    function all() {
        echo json_encode(generate_response(400, null, $this->JenisAnggota->find("all", [
                            "conditions" => [
                                "NOT" => [
                                    "JenisAnggota.id" => 1
                                ]
                            ]
        ])));
    }

}
