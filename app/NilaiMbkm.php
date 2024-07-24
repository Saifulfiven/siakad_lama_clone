<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NilaiMbkm extends Model
{
    protected $table = 'nilai_mbkm';
    protected $guarded = ['id'];

    public $timestamps = false;

    public function semester(){
        return $this->belongsTo('App\Semester','id_smt','id_smt');
    }

    public function mataKuliah(){
        return $this->belongsTo('App\Matakuliah','id_mk','id');
    }

    public function mkKurikulum(){
        return $this->belongsTo('App\MatakuliahKurikulum','id_mk','id');
    }
}