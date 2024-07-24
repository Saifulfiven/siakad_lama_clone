<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KrsStatus extends Model
{
	protected $fillable = ['id_mhs_reg', 'id_smt'];
	protected $table = 'krs_status';
	public $timestamps = false;
}
