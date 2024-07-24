<?php

namespace App\Http\Controllers\informasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth, Rmt;

class BerandaController extends Controller
{
    private $f = 'informasi.beranda.';
    private $prefix;

    function __construct(){
    	$this->prefix = env('DB_TABLE_PREFIX');
    }

    public function index()
    {
        return view($this->f."index");
    }
}