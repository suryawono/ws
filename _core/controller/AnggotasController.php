<?php

class AnggotasController extends Controller {

    function login() {
        $this->Anggota->data["Anggota"]["email"] = $_POST['email'];
        $this->Anggota->data["Anggota"]["token"] = $_POST['accessToken'];
        $this->Anggota->data["Anggota"]["gmail_id"] = $_POST['gmailID'];
        $this->Anggota->data["Anggota"]["nama"] = $_POST['name'];
        $this->Anggota->data["Anggota"]["pp_link"] = $_POST['ppLink'];
        $this->Anggota->data["Anggota"]["gender"] = isset($_POST['gender'])?$_POST['gender']:"e";
        $data = $this->Anggota->find("first", [
            "conditions" => [
                "and" => [
                    "Anggota.email" => $this->Anggota->data["Anggota"]["email"],
                ]
            ],
            "contains" => [
                "RumahTangga",
            ]
        ]);
        if (!empty($data)) {
            $this->Anggota->id = $data['Anggota']['id'];
        } else {
            $this->Anggota->RumahTangga->_add();
            $this->Anggota->data['Anggota']['jenis_anggota_id'] = 1;
            $this->Anggota->data['Anggota']['rumah_tangga_id'] = $this->Anggota->RumahTangga->getLastInsertID();
        }
        $this->Anggota->save();
        $data = $this->Anggota->find("first", [
            "conditions" => [
                "and" => [
                    "Anggota.email" => $this->Anggota->data["Anggota"]["email"],
                ]
            ],
            "contains" => [
                "RumahTangga",
                "JenisAnggota",
            ]
        ]);
        if (!empty($data)) {
            echo json_encode(generate_response(202, null, ["anggota" => $data['Anggota'], "rumah_tangga" => $data['RumahTangga'],"jenis_anggota"=>$data["JenisAnggota"]]));
        } else {
            echo json_encode(generate_response(402));
        }
    }

    function loginTest(){
        $data = $this->Anggota->find("first", [
            "conditions" => [
                "and" => [
                    "Anggota.email" => "suryawono@gmail.com",
                ]
            ],
            "contains" => [
                "RumahTangga",
                "JenisAnggota",
            ]
        ]);
        echo json_encode(generate_response(202, null, ["anggota" => $data['Anggota'], "rumah_tangga" => $data['RumahTangga'],"jenis_anggota"=>$data["JenisAnggota"]]));
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

    function add() {
        $this->Anggota->data = $this->buildQueryData();
        if ($this->Anggota->save()) {
            echo json_encode(generate_response(200));
        } else {
            echo json_encode(generate_response(101));
        }
    }

}
