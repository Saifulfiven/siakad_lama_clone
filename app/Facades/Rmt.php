<?php 

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Rmt extends Facade{
  protected static function getFacadeAccessor() { return 'rmt'; }
}