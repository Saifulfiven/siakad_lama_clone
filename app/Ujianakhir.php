<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ujianakhir extends Model
{
	protected $fillable = ['id','id_smt','id_mhs_reg','tgl_ujian','pukul','ruangan','judul_tmp','jenis','siap_seminar'];
    protected $table = 'ujian_akhir';

    public $timestamps = false;
}
