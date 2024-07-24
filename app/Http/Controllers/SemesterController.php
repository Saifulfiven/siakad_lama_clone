<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session, DB, Sia;

class SemesterController extends Controller
{
    public function index()
    {
    	$smt_mulai = '20171';
			$smt_now = Sia::idSemesterAktif();

			/**
			 * posisi semester sekarang, ganjil/genap
			 * @value => 1: ganjil atau 2: genap
			 */
			$posisi_smt = Sia::sessionPeriode('smt');

			$thn_mulai = substr($smt_mulai,0,4);
			$thn_now = substr($smt_now,0,4);

			$smt = ($thn_now - $thn_mulai + 1) * 2;

			if ( substr($smt_mulai,4) == 2 ) {
				$smt = $smt - 1;
			}

			if ( $posisi_smt == 1 ) {
				$smt = $smt - 1;
			}
			echo $posisi_smt;
			// echo $smt;
    }
}
