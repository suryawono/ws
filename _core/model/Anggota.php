<?php

class Anggota extends Model {

    var $belongsTo = [
        "RumahTangga",
        "JenisAnggota",
        "HubunganAnggota",
    ];
    var $hasMany = [
        "Transaksi",
    ];

    function login() {
        
    }

}
