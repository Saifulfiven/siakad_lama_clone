<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Aktivitas extends Model
{
	protected $fillable = ['id_smt','id_mhs_reg','ips','sks_smt','ipk','sks_total','status_mhs'];
    protected $table = 'aktivitas_kuliah';

    public $timestamps = false;
}
