<?php namespace App\Classes;
use Session, DB;

trait Custom
{

	public function sessionPeriode($field = 'id', $fakultas = '') {

		if ( empty($fakultas) ) {
			switch( $field ) {
				case 'id':
					$periode = Session::get('periode_aktif');
				break;

				case 'nama':
					$periode = Session::get('nm_periode_aktif');
				break;

				case 'smt':
					$periode = Session::get('posisi_periode');
				break;

				case 'berjalan':
					$periode = Session::get('periode_berjalan');
				break;

				case 'fakultas':
					$periode = Session::get('fakultas');
				break;

				default:

				break;
			}

		} else {
			$result = DB::table('semester_aktif as sa')
					->leftJoin('semester as smt','sa.id_smt','=','smt.id_smt')
					->select('sa.id_fakultas','smt.id_smt','smt.nm_smt','smt.smt')
					->where('sa.id_fakultas', $fakultas)->first();

			$periode = $result->id_smt;
		}

		return $periode;
	}

}