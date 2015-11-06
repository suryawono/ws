<?php

class AnggotasController extends Controller {

    function login() {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $result = $this->db->query(""
                . "select * from anggotas Anggota "
                . "where "
                . "Anggota.email='$email' and Anggota.password='$password'");
        if ($result->num_rows) {
            $data = array(
                "anggota" => buildResult($result->fetch_fields(), $result->fetch_row())
            );
            echo json_encode(generate_response(202, null, $data));
        } else {
            echo json_encode(generate_response(402));
        }
    }

}
