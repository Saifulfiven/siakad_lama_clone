<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Briva extends Model
{
  protected $table = 'briva';

  protected $dates = ['exp_date'];

  public function jenisBayar()
  {
  	return $this->hasOne('App\JenisBayar', 'id_jns_pembayaran', 'jenis_bayar');
  }

}
