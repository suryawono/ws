<?php

class RumahTangga extends Model {

    var $belongsTo = [
        "RumahTanggaStatus",
    ];
    var $hasMany = [
        "Anggota",
    ];
    
    var $virtualFields = array(
        "nama_kepala_rumah_tangga" => "select Ang.nama from anggotas as Ang where Ang.rumah_tangga_id=RumahTangga.id and Ang.jenis_anggota_id=1",
    );

    function _add() {
        $this->save();
        return $this->getLastInsertID();
    }
}
