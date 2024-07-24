<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
	protected $fillable = ['id_smt','nm_smt','smt'];
    protected $table = 'semester';

    public $timestamps = false;
}
