<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DokumenMhs extends Model
{
    protected $table = 'dokumen_mahasiswa';

    public function mhs()
    {
    	return $this->hasOne('App\Mahasiswa', 'id', 'id_mhs');
    }
}
