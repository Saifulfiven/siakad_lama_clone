<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
  protected $table = 'nilai';
  public $incrementing = false;
  public $timestamps = false;

  public function jadwalKuliah(){
      return $this->belongsTo('App\JadwalKuliah','id_jdk','id');
  }
}
