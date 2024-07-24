<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DaftarIjazah extends Model
{
    protected $fillable = ['id','id_mhs_reg','skripsi','turnitin','keterangan'];
    protected $table = 'pendaftaran_ijazah';
}