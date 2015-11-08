<?php

class Transaksi extends Model {
    
    var $belongsTo=[
        "Kategori",
        "Anggota",
    ];
    
    
    function login(){
    }
    
}
