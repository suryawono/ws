<?php

class TransaksisController extends Controller {

    function laporan() {
        $data = [];
        if (!isset($_POST['jenis'])) {
            $code = 405;
        } else {
            $jenis = $_POST['jenis'];
            switch ($jenis) {
                case "bulanan":
                    $tahun = isset($_POST['tahun']) ? $_POST['tahun'] : false;
                    $id = isset($_POST['id']) ? $_POST['id'] : false;
                    if ($tahun === false || $id === false) {
                        $code = 405;
                    } else {
                        $transaksi = $this->Transaksi->find("all", [
                            "conditions" => [
                                "and" => [
                                    "Transaksi.anggota_id" => $id,
                                    "Year(Transaksi.waktu)" => $tahun,
                                ]
                            ],
                            "fields" => [
                                "sum(CASE WHEN Kategori.jenis_kategori_id = 1 THEN Transaksi.besaran ELSE 0 END) as pemasukan",
                                "sum(CASE WHEN Kategori.jenis_kategori_id = 2 THEN Transaksi.besaran ELSE 0 END) as pengeluaran",
                                "Year(Transaksi.waktu) as tahun",
                                "Month(Transaksi.waktu) as bulan",
                            ],
                            "group" => [
                                "Year(Transaksi.waktu)",
                                "Month(Transaksi.waktu)"
                            ],
                            "joins" => [
                                "left" => [
                                    "Kategori"
                                ]
                            ],
                        ]);
                        $data = [];
                        $data['anggota_id'] = $id;
                        $data['transaksi'] = $transaksi;
                        $data['query'] = [
                            "jenis" => $jenis,
                            "tahun" => $tahun,
                            "id" => $id,
                        ];
                        $code = 400;
                    }
                    break;
                default :
                    $code = 405;
                    break;
            }
        }

        echo json_encode(generate_response($code, null, $data));
    }

    function tambah(){
        
    }
}
