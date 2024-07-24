<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sia, Rmt, DB, Response;

class KuesionerMasterController extends Controller
{
	/* komponen */
	    public function komponen(Request $r)
	    {
	    	$komponen = DB::table('kues_komponen as k')
	    						->leftJoin('prodi as pr', 'k.id_prodi', 'pr.id_prodi')
	    						->select('k.*', 'pr.jenjang','pr.nm_prodi')
	    						->orderBy('urutan');

	    	if ( $r->prodi ) {
	    		$komponen->where('k.id_prodi', $r->prodi);
	    	} else {
	    		$komponen->whereIn('k.id_prodi', Sia::getProdiUser());
	    	}

	    	$data['komponen'] = $komponen->paginate(10);

	    	$urutan_last = DB::table('kues_komponen')
	    					->whereIn('id_prodi', Sia::getProdiUser())
	    					->first();

	    	$data['urutan'] = !empty($urutan_last->urutan) ? $urutan_last->urutan : 1; 

	    	return view('kuesioner.master.komponen.index', $data);
	    }

	    public function storeKomponen(Request $r)
		{
			$this->validate($r, [
				'id_prodi' => 'required',
				'judul' => 'required',
				'jenis' => 'required',
				'urutan' => 'required'
			]);

			try {
				DB::transaction(function() use($r){

					$data = [
						'judul' => $r->judul,
						'urutan' => $r->urutan,
						'jenis' => $r->jenis,
						'id_prodi' => $r->id_prodi,
						'aktif' => $r->aktif
					];

					DB::table('kues_komponen')->insert($data);

				});
			} catch(\Exception $e) {
				Rmt::error('Gagal menyimpan : '.$e->getMessage());
				return redirect()->back();
			}

			Rmt::success('Berhasil menyimpan data');
			return redirect()->back();
		}

	    public function updateKomponen(Request $r)
	    {
			$this->validate($r, [
				'id_prodi' => 'required',
				'judul' => 'required',
				'jenis' => 'required',
				'urutan' => 'required'
			]);

			try {
				DB::transaction(function() use($r){

					$data = [
						'judul' => $r->judul,
						'urutan' => $r->urutan,
						'jenis' => $r->jenis,
						'id_prodi' => $r->id_prodi,
						'aktif' => $r->aktif
					];

					DB::table('kues_komponen')
						->where('id', $r->id)
						->update($data);

				});
			} catch(\Exception $e) {
				Rmt::error('Gagal menyimpan : '.$e->getMessage());
				return redirect()->back();
			}

	    	Rmt::success('Berhasil menyimpan data');
	    	return redirect()->back();
	    }

	    public function deleteKomponen(Request $r,$id)
	    {
	    	$rule = DB::table('kues_komponen_isi')
	    			->where('id_komponen',$id)->count();

	    	if ( $rule > 0 ) {
	    		Rmt::error('Gagal menghapus, terdapat isi pada komponen. Masuk ke menu Komponen Data');
	    		return redirect()->back();
	    	}

	    	DB::table('kues_komponen')->where('id',$id)->delete();
	    	Rmt::success('Berhasil menghapus data');
	    	return redirect()->back();
	    }
	/* End */

	/* komponen isi */
	    public function komponenIsi(Request $r)
	    {
	    	$komponen_isi = DB::table('kues_komponen_isi as kk')
	    						->leftJoin('kues_komponen as k', 'kk.id_komponen', 'k.id')
	    						->leftJoin('prodi as pr', 'pr.id_prodi', 'k.id_prodi')
	    						->select('kk.*','k.judul','pr.id_prodi','pr.jenjang','pr.nm_prodi');

	    	if ( $r->prodi ) {
	    		$komponen_isi->where('k.id_prodi', $r->prodi);
	    	} else {
	    		$komponen_isi->whereIn('k.id_prodi', Sia::getProdiUser());
	    	}

	    	if ( $r->komponen ) {
	    		$komponen_isi->where('kk.id_komponen', $r->komponen);
	    	}
	    	
	    	$data['komponen_isi'] = $komponen_isi->orderBy('kk.id_komponen')
	    							->orderBy('kk.urutan')
	    							->paginate(10);

	    	$data['komponen'] = DB::table('kues_komponen')
	    						->whereIn('id_prodi', Sia::getProdiUser())
	    						->orderBy('urutan')->get();

	    	return view('kuesioner.master.komponen.isi', $data);
	    }

	    public function storeKomponenIsi(Request $r)
		{
			$this->validate($r, [
				'id_komponen' => 'required',
				'pertanyaan' => 'required',
				'urutan' => 'required',
				'aktif' => 'required'
			]);

			try {
				DB::transaction(function() use($r){

					$data = [
						'id_komponen' => $r->id_komponen,
						'urutan' => $r->urutan,
						'pertanyaan' => $r->pertanyaan,
						'aktif' => $r->aktif,
					];

					DB::table('kues_komponen_isi')->insert($data);

				});
			} catch(\Exception $e) {
				Rmt::error('Gagal menyimpan : '.$e->getMessage());
				return redirect()->back();
			}

			Rmt::success('Berhasil menyimpan data');
			return redirect()->back();
		}

	    public function updateKomponenIsi(Request $r)
	    {
			$this->validate($r, [
				'id_komponen' => 'required',
				'pertanyaan' => 'required',
				'urutan' => 'required',
				'aktif' => 'required'
			]);

			try {
				DB::transaction(function() use($r){

					$data = [
						'id_komponen' => $r->id_komponen,
						'urutan' => $r->urutan,
						'pertanyaan' => $r->pertanyaan,
						'aktif' => $r->aktif,
					];

					DB::table('kues_komponen_isi')
						->where('id', $r->id)
						->update($data);

				});
			} catch(\Exception $e) {
				Rmt::error('Gagal menyimpan : '.$e->getMessage());
				return redirect()->back();
			}

	    	Rmt::success('Berhasil menyimpan data');
	    	return redirect()->back();
	    }

	    public function deleteKomponenIsi(Request $r,$id)
	    {
	    	$rule = DB::table('kues_hasil')
	    			->where('id_komponen_isi',$id)->count();

	    	if ( $rule > 0 ) {
	    		Rmt::error('Gagal menghapus, data ini telah diisi oleh mahasiswa. Silahkan non aktifkan apabila tidak dipakai.');
	    		return redirect()->back();
	    	}

	    	DB::table('kues_komponen_isi')->where('id',$id)->delete();
	    	Rmt::success('Berhasil menghapus data');
	    	return redirect()->back();
	    }
	/* End */

	/* Jadwal */
	    public function jadwal(Request $r)
	    {
	    	$kuesioner = DB::table('kues_jadwal as k')
	    						->leftJoin('prodi as pr', 'k.id_prodi', 'pr.id_prodi')
	    						->select('k.*', 'pr.jenjang','pr.nm_prodi')
	    						->orderBy('id_smt','desc');

	    	if ( $r->prodi ) {
	    		$kuesioner->where('k.id_prodi', $r->prodi);
	    	} else {
	    		$kuesioner->whereIn('k.id_prodi', Sia::getProdiUser());
	    	}

	    	$data['kuesioner'] = $kuesioner->paginate(10);

	    	return view('kuesioner.master.jadwal.index', $data);
	    }

	    public function storeJadwal(Request $r)
		{
			$this->validate($r, [
				'ket' => 'required',
				'id_prodi' => 'required',
				'ta' => 'required'
			]);

			try {
				DB::transaction(function() use($r){

					// set aktif 0 for all by prodi
					DB::table('kues_jadwal')
						->where('id_prodi', $r->id_prodi)
						->update(['aktif' => 0]);

					$data = [
						'ket' => $r->ket,
						'id_prodi' => $r->id_prodi,
						'id_smt' => $r->ta,
						'aktif' => $r->aktif,
					];

					DB::table('kues_jadwal')->insert($data);

				});
			} catch(\Exception $e) {
				Rmt::error('Gagal menyimpan : '.$e->getMessage());
				return redirect()->back();
			}

			Rmt::success('Berhasil menyimpan data');
			return redirect()->back();
		}

	    public function updateJadwal(Request $r)
	    {
			$this->validate($r, [
				'ket' => 'required',
				'id_prodi' => 'required',
				'ta' => 'required'
			]);

			try {
				DB::transaction(function() use($r){

					if ( $r->aktif == 1 ) {
						// set aktif 0 for all by prodi
						DB::table('kues_jadwal')
							->where('id_prodi', $r->id_prodi)
							->update(['aktif' => 0]);
					}
					$data = [
						'ket' => $r->ket,
						'id_prodi' => $r->id_prodi,
						'id_smt' => $r->ta,
						'aktif' => $r->aktif,
					];

					DB::table('kues_jadwal')
						->where('id', $r->id)
						->update($data);

				});
			} catch(\Exception $e) {
				Rmt::error('Gagal menyimpan : '.$e->getMessage());
				return redirect()->back();
			}

	    	Rmt::success('Berhasil menyimpan data');
	    	return redirect()->back();
	    }

	    public function deleteJadwal(Request $r,$id)
	    {
	    	$rule = DB::table('kues')
	    			->where('id_kues_jadwal',$id)->count();

	    	if ( $rule > 0 ) {
	    		Rmt::error('Gagal menghapus, terdapat isi pada kuesioner. Masuk ke menu Hasil Kuesioner');
	    		return redirect()->back();
	    	}

	    	DB::table('kues_jadwal')->where('id',$id)->delete();
	    	Rmt::success('Berhasil menghapus data');
	    	return redirect()->back();
	    }
	/* End */

	public function ajax(Request $r)
	{
		switch ($r->tipe) {
			case 'urut-komponen':
				$data = DB::table('kues_komponen')
						->where('id_prodi', $r->prodi)
						->where('aktif', '1')
						->orderBy('urutan', 'desc')
						->first();

				if ( !empty($data->urutan) ) {
					echo $data->urutan + 1;
				} else {
					echo 1;
				}

			break;
			case 'urut-komponen-isi':
				$data = DB::table('kues_komponen_isi')
						->where('id_komponen', $r->komponen)
						->where('aktif', '1')
						->orderBy('urutan', 'desc')
						->first();

				if ( !empty($data->urutan) ) {
					echo $data->urutan + 1;
				} else {
					echo 1;
				}

			break;
			
			default:
				# code...
				break;
		}
	}
}
