<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penguji extends Model
{
	protected $fillable = ['id_smt', 'id_mhs_reg','id_dosen','jabatan','nilai','jenis','setuju'];
    protected $table = 'penguji';

    public $timestamps = false;
}
