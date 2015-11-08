<?php

class RumahTanggasController extends Controller {


    function get() {
        $id = $_POST['id'];
        $data = $this->RumahTangga->find("first", [
            "conditions" => [
                "RumahTangga.id" => $id,
            ],
            "contains" => [
                "RumahTanggaStatus",
                "Anggota"=>[
                    "RumahTangga"=>[
                        "Anggota",
                    ],
                    "JenisAnggota",
                    "HubunganAnggota",
                    "Transaksi"=>[
                        "Kategori",
                    ],
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
