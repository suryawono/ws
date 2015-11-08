<?php

class HubunganAnggotasController extends Controller {

    function all() {
        echo json_encode(generate_response(400, null, $this->HubunganAnggota->find("all")));
    }

}
