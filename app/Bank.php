<?php
// test
namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'bank';

    public $incrementing = false;
    public $timestamps = false;
}
