<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{

  protected $table = 'dosen';

  public $incrementing = false;

  protected $dates = ['tgl_lahir'];

}
