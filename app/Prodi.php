<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    protected $table = 'prodi';

    public $timestamps = false;

    public function fakultas()
    {
    	return $this->hasOne('App\Fakultas', 'id', 'id_fakultas');
    }
}
