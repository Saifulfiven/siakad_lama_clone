<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Matakuliah extends Model
{

  protected $table = 'matakuliah';

  public $incrementing = false;

  // protected $dates = ['tgl_mulai_efektif', 'tgl_akhir_efektif'];
}
