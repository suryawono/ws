<?php

class AnggotasController extends Controller {

    function login() {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $data = $this->Anggota->find("first", [
            "conditions" => [
                "and" => [
                    "Anggota.email" => $email,
                    "Anggota.password" => $password,
                ]
            ],
            "contains" => [
                "RumahTangga",
            ]
        ]);
        if (!empty($data)) {
            echo json_encode(generate_response(202, null, ["anggota" => $data['Anggota'], "rumah_tangga" => $data['RumahTangga']]));
        } else {
            echo json_encode(generate_response(402));
        }
    }

    function get() {
        $id = $_POST['id'];
        $data = $this->Anggota->find("first", [
            "conditions" => [
                "Anggota.id" => $id,
            ],
            "contains" => [
                "RumahTangga" => [
                    "RumahTanggaStatus",
                    "Anggota",
                ],
                "JenisAnggota",
                "HubunganAnggota",
                "Transaksi" => [
                    "Kategori",
                ],
            ]
        ]);
        if (!empty($data)) {
            echo json_encode(generate_response(400, null, $data));
        } else {
            echo json_encode(generate_response(401));
        }
    }

    
}
