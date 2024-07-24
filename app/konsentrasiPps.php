<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class konsentrasiPps extends Model
{
    protected $table = 'konsentrasi_pps';

    public function konsen()
    {
    	return $this->belongsTo('App\Konsentrasi', 'id_konsentrasi', 'id_konsentrasi');
    }
}
