<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sia, Rmt, DB, Response;
use App\Bank, App\Biaya;

class MasterKeuanganController extends Controller
{
	/* Bank */
	    public function bank(Request $r)
	    {

	    	$data['bank'] = Bank::all();

	    	return view('keuangan.master.bank', $data);
	    }

	    public function storeBank(Request $r)
		{
			$this->validate($r, [
				'nm_bank' => 'required',
			]);

			try {
				DB::transaction(function() use($r){

					$last_bank = Bank::orderBy('id','desc')->first();
					$last_id = $last_bank->id;
					$last_id++;

					$data = new Bank;
					$data->id = $last_id;
					$data->nm_bank = $r->nm_bank;
					$data->save();

				});
			} catch(\Exception $e) {
				Rmt::error('Gagal menyimpan : '.$e->getMessage());
				return redirect()->back();
			}

			Rmt::success('Berhasil menyimpan data');
			return redirect()->back();
		}

	    public function updateBank(Request $r)
	    {
			$this->validate($r, [
				'nm_bank' => 'required',
			]);

			try {
				DB::transaction(function() use($r){
					$data = ['nm_bank' => $r->nm_bank];
					DB::table('bank')->where('id', $r->id)->update($data);
				});
			} catch(\Exception $e) {
				Rmt::error('Gagal menyimpan : '.$e->getMessage());
				return redirect()->back();
			}

	    	Rmt::success('Berhasil menyimpan data');
	    	return redirect()->back();
	    }

	    public function deleteBank($id)
	    {
	    	$rule = DB::table('pembayaran')->where('id_bank',$id)->count();

	    	if ( $rule > 0 ) {
	    		Rmt::error('Gagal menghapus, bank sedang terpakai pada modul pembayaran');
	    		return redirect()->back();
	    	}

	    	Bank::where('id',$id)->delete();
	    	Rmt::success('Berhasil menghapus data');
	    	return redirect()->back();
	    }
	/* End Bank */

	/* Setting biaya kuliah */

	    public function biaya(Request $r)
	    {

	    	$query = Biaya::whereIn('id_prodi', Sia::getProdiUser())->orderBy('tahun', 'desc');

	    	if ( !empty($r->prodi) ) {
	    		$query->where('id_prodi', $r->prodi);
	    	}

	    	if ( !empty($r->tahun) ) {
	    		$query->where('tahun', $r->tahun);
	    	}
	    	
	    	$data['biaya'] = $query->paginate(10);

	    	return view('keuangan.master.biaya', $data);
	    }

	    public function editBiaya(Request $r)
	    {

	    	$biaya = Biaya::where('tahun', $r->tahun)
	    				->where('id_prodi', $r->id_prodi)
	    				->first();
	    	?>
	            <?= csrf_field() ?>

	            <input type="hidden" name="tahun" value="<?= $r->tahun ?>">
	            <input type="hidden" name="id_prodi" value="<?= $r->id_prodi ?>">

	            <table class="table" width="100%" border="0">
	            	<tr>
	            		<td style="padding: 10px 0">Tahun</td>
	            		<td><?= $r->tahun ?></td>
	            	</tr>
	            	<tr>
	            		<td>SPP</td>
	            		<td>
	            			<input type="text" name="spp" value="<?= Rmt::rupiah($biaya->spp) ?>" class="form-control biaya">
	            		</td>
	            	</tr>
	            	<tr>
	            		<td>BPP</td>
	            		<td><input type="text" name="bpp" value="<?= Rmt::rupiah($biaya->bpp) ?>" class="form-control biaya"></td>
	            	</tr>
	            	<tr>
	            		<td>Seragam</td>
	            		<td><input type="text" name="seragam" value="<?= Rmt::rupiah($biaya->seragam) ?>" class="form-control biaya"></td>
	            	</tr>
	            	<tr>
	            		<td>Lainnya</td>
	            		<td><input type="text" name="lainnya" value="<?= Rmt::rupiah($biaya->lainnya) ?>" class="form-control biaya"></td>
	            	</tr>
	            </table>

		    <?php
	    }

	    public function updateBiaya(Request $r)
		{
			$this->validate($r, [
				'spp' => 'required',
				'bpp' => 'required',
				'seragam' => 'required',
			]);

			try {
				DB::transaction(function() use($r){

					$spp = str_replace('.', '', $r->spp);
					$spp2 = str_replace(',', '', $spp);

					$bpp = str_replace('.', '', $r->bpp);
					$bpp2 = str_replace(',', '', $bpp);

					$seragam = str_replace('.', '', $r->seragam);
					$seragam2 = str_replace(',', '', $seragam);

					$lainnya = str_replace('.', '', $r->lainnya);
					$lainnya2 = str_replace(',', '', $lainnya);

					$data = [
						'spp' => $spp2,
						'bpp' => $bpp2,
						'seragam' => $seragam2,
						'lainnya' => $lainnya2,
					];
					
					DB::table('biaya_kuliah')
						->where('tahun', $r->tahun)
						->where('id_prodi', $r->id_prodi)
						->update($data);

				});
			} catch(\Exception $e) {
				Rmt::error('Gagal menyimpan : '.$e->getMessage());
				return redirect()->back();
			}

			Rmt::success('Berhasil menyimpan data');
			return redirect()->back();
		}
}