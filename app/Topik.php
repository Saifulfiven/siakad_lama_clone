<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Topik extends Model
{
    protected $table = 'lms_topik';

    public function jawaban()
    {
    	return $this->hasMany('App\TopikJawaban', 'id_topik', 'id');
    }
}
