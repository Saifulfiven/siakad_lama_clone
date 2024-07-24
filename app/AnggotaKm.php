<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnggotaKm extends Model
{
    protected $table = 'km_anggota_aktivitas';

    public $timestamps = false;

    public function mhsreg()
    {
        return $this->belongsTo('App\Mahasiswareg', 'id_mhs_reg', 'id');
    }
}
