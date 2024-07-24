<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AktivitasKm extends Model
{
    protected $table = 'km_aktivitas';

    public function smt()
    {
        return $this->belongsTo('App\Semester', 'id_smt', 'id_smt');
    }
}
