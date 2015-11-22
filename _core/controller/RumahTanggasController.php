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
                "Anggota" => [
                    "RumahTangga" => [
                        "Anggota",
                    ],
                    "JenisAnggota",
                    "HubunganAnggota",
                    "Transaksi" => [
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

    function edit() {
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $deskripsi = $_POST['deskripsi'];
        $alamat = $_POST['alamat'];
        $setup_up = $_POST['setup_up'];
        $this->RumahTangga->id = $id;
        $this->RumahTangga->data['RumahTangga'] = [
            "nama" => $nama,
            "deskripsi" => $deskripsi,
            "alamat" => $alamat,
            "setup_up"=>$setup_up,
        ];
        if ($this->RumahTangga->save()) {
            echo json_encode(generate_response(200));
        }else{
            echo json_encode(generate_response(101));
        }
    }

}
