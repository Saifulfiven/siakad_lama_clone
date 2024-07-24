<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
  protected $table = 'mahasiswa';

  public $incrementing = false;

  protected $softDeletes = true;

  protected $dates = ['tgl_lahir','tgl_lahir_ibu','tgl_lahir_ayah','tgl_lahir_wali'];

}
