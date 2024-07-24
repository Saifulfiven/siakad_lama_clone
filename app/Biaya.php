<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Biaya extends Model
{
    protected $table = 'biaya_kuliah';

    public $timestamps = false;

    public function prodi()
    {
    	return $this->belongsTo('App\Prodi', 'id_prodi', 'id_prodi');
    }
}