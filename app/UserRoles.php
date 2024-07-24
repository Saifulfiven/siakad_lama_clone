<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRoles extends Model
{
    protected $table = 'user_roles';

    public $incrementing = false;
    public $timestamps = false;

    public function prodi()
    {
    	return $this->belongsTo('App\Prodi', 'id_prodi', 'id_prodi');
    }
}