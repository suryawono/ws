<?php

class AnggotasController extends Controller {

    function login() {
        $this->Anggota->data["Anggota"]["email"] = $_POST['email'];
        $this->Anggota->data["Anggota"]["token"] = $_POST['accessToken'];
        $this->Anggota->data["Anggota"]["gmail_id"] = $_POST['gmailID'];
        $this->Anggota->data["Anggota"]["nama"] = $_POST['name'];
        $this->Anggota->data["Anggota"]["pp_link"] = $_POST['ppLink'];
        $this->Anggota->data["Anggota"]["gender"] = $_POST['gender'];
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
            $this->Anggota->data['Anggota']['rumah_tangga_id'] = $this->Anggota->RumahTangga->getLastInsertID();
        }
        if (isset($this->Anggota->data['Anggota']['jenis_anggota_id'])) {
            
        } else {
            $this->Anggota->data['Anggota']['jenis_anggota_id'] = 1;
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

    function add() {
        $this->Anggota->data = $_POST['data'];
        if ($this->Anggota->save()) {
            echo json_encode(generate_response(200));
        } else {
            echo json_encode(generate_response(101));
        }
    }

}
