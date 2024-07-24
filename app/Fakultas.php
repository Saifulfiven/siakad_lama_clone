<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fakultas extends Model
{
    protected $table = 'fakultas';

    public $timestamps = false;

    public function prodi()
    {
    	return $this->hasMany('App\Prodi', 'id');
    }
}
