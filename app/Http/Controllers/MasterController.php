<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sia, Rmt, DB, Response;
use App\Prodi, App\Fakultas, App\Konsentrasi, App\Kelas, App\Jamkuliah, App\SkalaNilai, App\Ruangan, App\Semester;

class MasterController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	/* Prodi */

	public function prodi(Request $r)
	{
		$prodi = Prodi::whereNotNull('id_prodi');

		if (!Sia::admin()) {
			$prodi->where('id_fakultas', Sia::getFakultasUser());
		}

		$data['prodi'] = $prodi->get();

		return view('master.prodi.index', $data);
	}

	public function storeProdi(Request $r)
	{
		$this->validate($r, [
			'id_prodi' => 'required|unique:prodi',
			'nm_prodi' => 'required',
			'jenjang' => 'required',
			'gelar' => 'required',
			'kode_nim' => 'required',
			'sk_akreditasi' => 'required',
			'ketua_prodi' => 'required',
		]);

		try {
			DB::transaction(function () use ($r) {

				$data = new Prodi;
				$data->id_fakultas = $r->id_fakultas;
				$data->id_prodi = $r->id_prodi;
				$data->nm_prodi = $r->nm_prodi;
				$data->jenjang = $r->jenjang;
				$data->gelar = $r->gelar;
				$data->kode_nim = $r->kode_nim;
				$data->sk_akreditasi = $r->sk_akreditasi;
				$data->ketua_prodi = $r->ketua_prodi;
				$data->nip_ketua_prodi = $r->nip_ketua_prodi;
				$data->save();
			});
		} catch (\Exception $e) {
			return Response::json(['error' => 1, 'msg' => $e->getMessage()], 200);
		}

		Rmt::success('Berhasil menyimpan data');
		$response = ['error' => 0, 'msg' => 'sukses'];
		return Response::json($response, 200);
	}

	public function editProdi($id)
	{
		$prodi = Prodi::where('id_prodi', $id)->first(); ?>

		<form action="<?= route('m_prodi_update') ?>" id="form-prodi" method="post">
			<?= csrf_field() ?>
			<input type="hidden" name="id" value="<?= $id ?>">

			<div class="table-responsive">
				<table border="0" class="table table-hover table-form">
					<tr class="pindahan">
						<td>Kode Prodi <span>*</span></td>
						<td>
							<input type="text" name="id_prodi" disabled="" value="<?= $prodi->id_prodi ?>" class="form-control mw-1">
						</td>
					</tr>
					<tr class="pindahan">
						<td>Nama Prodi <span>*</span></td>
						<td>
							<input type="text" name="nm_prodi" value="<?= $prodi->nm_prodi ?>" class="form-control">
						</td>
					</tr>
					<tr>
						<td>Jenjang <span>*</span></td>
						<td>
							<select class="form-control select-jenis-daftar mw-1" name="jenjang">
								<?php foreach (Sia::jenjang() as $jp) { ?>
									<option value="<?= $jp ?>" <?= $jp == $prodi->jenjang ? 'selected' : '' ?>><?= $jp ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Gelar Akademik <span>*</span></td>
						<td>
							<input type="text" name="gelar" value="<?= $prodi->gelar ?>" class="form-control">
						</td>
					</tr>
					<tr>
						<td>Kode NIM <span>*</span>
							<p style="font-size: 15px">xxxx<span style="color:red;font-size: 15px"> 21 </span>xxxx</p>
						</td>
						<td>
							<input type="text" name="kode_nim" value="<?= $prodi->kode_nim ?>" class="form-control mw-1">
						</td>
					</tr>
					<tr>
						<td>No. SK Akreditasi <span>*</span></td>
						<td>
							<input type="text" name="sk_akreditasi" value="<?= $prodi->sk_akreditasi ?>" class="form-control">
						</td>
					</tr>
					<tr>
						<td>Ketua Prodi <span>*</span></td>
						<td>
							<input type="text" name="ketua_prodi" value="<?= $prodi->ketua_prodi ?>" class="form-control">
						</td>
					</tr>
					<tr>
						<td>NIP Ketua Prodi <span>*</span></td>
						<td>
							<input type="text" name="nip_ketua_prodi" value="<?= $prodi->nip_ketua_prodi ?>" class="form-control">
						</td>
					</tr>
				</table>
			</div>
			<hr>
			<button type="submit" id="btn-submit" class="pull-right btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
		</form>
	<?php
	}

	public function updateProdi(Request $r)
	{
		$this->validate($r, [
			'nm_prodi' => 'required',
			'jenjang' => 'required',
			'gelar' => 'required',
			'kode_nim' => 'required',
			'sk_akreditasi' => 'required',
			'ketua_prodi' => 'required'
		]);

		$data = ['nm_prodi' => $r->nm_prodi, 'jenjang' => $r->jenjang, 'gelar' => $r->gelar, 'kode_nim' => $r->kode_nim, 'sk_akreditasi' => $r->sk_akreditasi, 'ketua_prodi' => $r->ketua_prodi, 'nip_ketua_prodi' => $r->nip_ketua_prodi];
		DB::table('prodi')->where('id_prodi', $r->id)->update($data);

		Rmt::success('Berhasil menyimpan data');
		return redirect()->back();
	}

	public function deleteProdi($id)
	{
		$rule = DB::table('mahasiswa_reg')->where('id_prodi', $id)->count();
		$rule2 = DB::table('jadwal_kuliah')->where('id_prodi', $id)->count();
		$rule3 = DB::table('matakuliah')->where('id_prodi', $id)->count();
		$rule4 = DB::table('kurikulum')->where('id_prodi', $id)->count();

		if ($rule + $rule2 + $rule3 + $rule4 > 0) {
			Rmt::error('Gagal menghapus, prodi sedang terpakai pada modul lain');
			return redirect()->back();
		}

		Prodi::where('id_prodi', $id)->delete();
		Rmt::success('Berhasil menghapus data');
		return redirect()->back();
	}
	/* End Prodi */

	/* Fakultas */
	public function fakultas(Request $r)
	{
		$data['fakultas'] = Fakultas::all();

		return view('master.fakultas.index', $data);
	}

	public function storeFakultas(Request $r)
	{
		$this->validate($r, [
			'nm_fakultas' => 'required',
		]);

		try {
			DB::transaction(function () use ($r) {

				$data = new Fakultas;
				$data->nm_fakultas = $r->nm_fakultas;
				$data->save();
			});
		} catch (\Exception $e) {
			Rmt::error('Gagal menyimpan : ' . $e->getMessage());
			return redirect()->back();
		}

		Rmt::success('Berhasil menyimpan data');
		return redirect()->back();
	}

	public function updateFakultas(Request $r)
	{
		$this->validate($r, [
			'nm_fakultas' => 'required',
		]);

		$data = ['nm_fakultas' => $r->nm_fakultas];
		DB::table('fakultas')->where('id', $r->id)->update($data);

		Rmt::success('Berhasil menyimpan data');
		return redirect()->back();
	}

	public function deleteFakultas($id)
	{
		$rule = DB::table('prodi')->where('id_fakultas', $id)->count();

		if ($rule > 0) {
			Rmt::error('Gagal menghapus, fakultas sedang terpakai pada master prodi');
			return redirect()->back();
		}

		Fakultas::where('id', $id)->delete();
		Rmt::success('Berhasil menghapus data');
		return redirect()->back();
	}
	/* End Fakultas */

	/* Konsentrasi */

	public function konsentrasi(Request $r)
	{
		$konsentrasi = DB::table('konsentrasi as k')
			->leftJoin('prodi as p', 'k.id_prodi', 'p.id_prodi')
			->whereIn('k.id_prodi', Sia::getProdiUser());

		if (!empty($r->prodi)) {
			$konsentrasi->where('k.id_prodi', $r->prodi);
		}

		$data['konsentrasi'] = $konsentrasi->get();

		return view('master.konsentrasi.index', $data);
	}

	public function storeKonsentrasi(Request $r)
	{
		$this->validate($r, [
			'prodi' => 'required',
			'nm_konsentrasi' => 'required',
		]);

		try {
			DB::transaction(function () use ($r) {

				$data = new Konsentrasi;
				$data->id_prodi = $r->prodi;
				$data->nm_konsentrasi = $r->nm_konsentrasi;
				$data->save();
			});
		} catch (\Exception $e) {
			return Response::json(['error' => 1, 'msg' => $e->getMessage()], 200);
		}

		Rmt::success('Berhasil menyimpan data');
		$response = ['error' => 0, 'msg' => 'sukses'];
		return Response::json($response, 200);
	}

	public function editKonsentrasi($id)
	{
		$kon = Konsentrasi::where('id_konsentrasi', $id)->first(); ?>

		<form action="<?= route('m_konsentrasi_update') ?>" method="post">
			<?= csrf_field() ?>
			<input type="hidden" name="id" value="<?= $id ?>">

			<div class="table-responsive">
				<table border="0" class="table table-hover table-form">
					<tr>
						<td>Nama Prodi <span>*</span></td>
						<td>
							<select class="form-control" name="prodi">
								<option value="">-- Pilih program studi --</option>
								<?php foreach (Sia::listProdi() as $f) { ?>
									<option value="<?= $f->id_prodi ?>" <?= $f->id_prodi == $kon->id_prodi ? 'selected' : '' ?>><?= $f->jenjang . ' ' . $f->nm_prodi ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Nama Konsentrasi <span>*</span></td>
						<td>
							<input type="text" name="nm_konsentrasi" value="<?= $kon->nm_konsentrasi ?>" class="form-control">
						</td>
					</tr>
					<tr>
						<td>Status <span>*</span></td>
						<td>
							<select class="form-control" name="status">
								<option value="1" <?= $kon->aktif == '1' ? 'selected' : '' ?>">Aktif</option>
								<option value="0" <?= $kon->aktif == '0' ? 'selected' : '' ?>">Non-Aktif</option>
							</select>
						</td>
					</tr>
				</table>
			</div>
			<hr>
			<button type="submit" id="btn-submit" class="pull-right btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
		</form>
	<?php
	}

	public function updateKonsentrasi(Request $r)
	{
		$this->validate($r, [
			'prodi' => 'required',
			'nm_konsentrasi' => 'required',
		]);

		$data = ['id_prodi' => $r->prodi, 'nm_konsentrasi' => $r->nm_konsentrasi, 'aktif' => $r->status];
		DB::table('konsentrasi')->where('id_konsentrasi', $r->id)->update($data);

		Rmt::success('Berhasil menyimpan data');
		return redirect()->back();
	}

	public function deleteKonsentrasi($id)
	{
		$rule = DB::table('mahasiswa_reg')->where('id_konsentrasi', $id)->count();

		if ($rule > 0) {
			Rmt::error('Gagal menghapus, prodi sedang terpakai pada modul lain');
			return redirect()->back();
		}

		Konsentrasi::where('id_konsentrasi', $id)->delete();
		Rmt::success('Berhasil menghapus data');
		return redirect()->back();
	}
	/* End Konsentrasi */


	/* Skala Nilai */

	public function skalaNilai(Request $r)
	{
		$query = DB::table('skala_nilai as sn')
			->leftJoin('prodi as p', 'sn.id_prodi', 'p.id_prodi')
			->whereIn('sn.id_prodi', Sia::getProdiUser());

		if (!empty($r->prodi)) {
			$query->where('sn.id_prodi', $r->prodi);
		}

		$data['skala'] = $query->orderBy('sn.id_prodi')->orderBy('sn.nilai_huruf')->get();

		return view('master.skala-nilai.index', $data);
	}

	public function storeSkalaNilai(Request $r)
	{
		$this->validate($r, [
			'prodi' => 'required',
			'nilai_huruf' => 'required',
			'nilai_indeks' => 'required',
			'range_nilai' => 'required',
			'range_atas' => 'required',
		]);

		try {
			DB::transaction(function () use ($r) {

				$data = new SkalaNilai;
				$data->id_prodi = $r->prodi;
				$data->nilai_huruf = $r->nilai_huruf;
				$data->nilai_indeks = $r->nilai_indeks;
				$data->range_nilai = $r->range_nilai;
				$data->range_atas = $r->range_atas;
				$data->save();
			});
		} catch (\Exception $e) {
			return Response::json(['error' => 1, 'msg' => $e->getMessage()], 200);
		}

		Rmt::success('Berhasil menyimpan data');
		$response = ['error' => 0, 'msg' => 'sukses'];
		return Response::json($response, 200);
	}

	public function editSkalaNilai($id)
	{
		$kon = SkalaNilai::where('id_konsentrasi', $id)->first(); ?>

		<form action="<?= route('m_skalanilai_update') ?>" method="post">
			<?= csrf_field() ?>
			<input type="hidden" name="id" value="<?= $id ?>">

			<div class="table-responsive">
				<table border="0" class="table table-hover table-form">
					<tr>
						<td>Nama Prodi <span>*</span></td>
						<td>
							<select class="form-control" name="prodi">
								<option value="">-- Pilih program studi --</option>
								<?php foreach (Sia::listProdi() as $f) { ?>
									<option value="<?= $f->id_prodi ?>" <?= $f->id_prodi == $kon->id_prodi ? 'selected' : '' ?>><?= $f->jenjang . ' ' . $f->nm_prodi ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Nilai Huruf <span>*</span></td>
						<td>
							<input type="text" name="nilai_huruf" value="<?= $kon->nilai_huruf ?>" class="form-control mw-1">
						</td>
					</tr>
					<tr>
						<td>Nilai Indeks <span>*</span></td>
						<td>
							<input type="text" name="nilai_indeks" value="<?= $kon->nilai_huruf ?>" class="form-control mw-2 number">
						</td>
					</tr>
					<tr>
						<td>Range Nilai <span>*</span></td>
						<td>
							<input type="text" name="range_nilai" value="<?= $kon->range_nilai ?>" class="form-control mw-2">
						</td>
					</tr>
					<tr>
						<td>Range Atas <span>*</span></td>
						<td>
							<input type="text" name="range_atas" value="<?= $kon->range_atas ?>" class="form-control mw-2 number">
						</td>
					</tr>
				</table>
			</div>
			<hr>
			<button type="submit" id="btn-submit" class="pull-right btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
		</form>
		<script>
			$(function() {
				$('.number').keypress(function(event) {
					if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
						event.preventDefault();
					}
				});
			});
		</script>
<?php
	}

	public function updateSkalaNilai(Request $r)
	{
		$this->validate($r, [
			'prodi' => 'required',
			'nilai_huruf' => 'required',
			'nilai_indeks' => 'required',
			'range_nilai' => 'required',
			'range_atas' => 'required',
		]);

		$data = ['id_prodi' => $r->prodi, 'nilai_huruf' => $r->nilai_huruf, 'nilai_indeks' => $r->nilai_indeks, 'range_nilai' => $r->range_nilai, 'range_atas' => $r->range_atas];
		DB::table('skala_nilai')->where('id', $r->id)->update($data);

		Rmt::success('Berhasil menyimpan data');
		return redirect()->back();
	}

	public function deleteSkalaNilai($id)
	{
		$rule = DB::table('mahasiswa_reg')->where('id_konsentrasi', $id)->count();

		SkalaNilai::where('id', $id)->delete();
		Rmt::success('Berhasil menghapus data');
		return redirect()->back();
	}
	/* End skala */

	/* Kelas */
	public function kelas(Request $r)
	{
		$data['kelas'] = DB::table('kelas as k')
			->leftJoin('prodi as pr', 'pr.id_prodi', 'k.id_prodi')
			->whereIn('k.id_prodi', Sia::getProdiUser())
			->paginate();

		return view('master.kelas.index', $data);
	}

	public function storeKelas(Request $r)
	{
		$this->validate($r, [
			'prodi' => 'required',
			'nm_kelas' => 'required',
			'ket' => 'required'
		]);

		$rule = Kelas::where('id_prodi', $r->prodi)
			->where('ket', $r->ket)
			->where('nm_kelas', $r->nm_kelas)->count();
		if ($rule > 0) {
			Rmt::error('Gagal menyimpan, Kelas telah ada');
			return redirect()->back();
		}

		try {
			DB::transaction(function () use ($r) {

				$data = new Kelas;
				$data->id_prodi = $r->prodi;
				$data->nm_kelas = $r->nm_kelas;
				$data->ket 		= $r->ket;
				$data->save();
			});
		} catch (\Exception $e) {
			Rmt::error('Gagal menyimpan : ' . $e->getMessage());
			return redirect()->back();
		}

		Rmt::success('Berhasil menyimpan data');
		return redirect()->back();
	}

	public function updateKelas(Request $r)
	{
		$this->validate($r, [
			'prodi' => 'required',
			'nm_kelas' => 'required',
			'ket' => 'required'
		]);

		$rule = Kelas::where('id_prodi', $r->prodi)
			->where('ket', $r->ket)
			->where('nm_kelas', $r->nm_kelas)
			->where('id', '<>', $r->id)->count();
		if ($rule > 0) {
			Rmt::error('Gagal menyimpan, Kelas telah ada');
			return redirect()->back();
		}

		try {
			DB::transaction(function () use ($r) {

				$data = Kelas::find($r->id);
				$data->id_prodi = $r->prodi;
				$data->nm_kelas = $r->nm_kelas;
				$data->ket 		= $r->ket;
				$data->save();
			});
		} catch (\Exception $e) {
			Rmt::error('Gagal menyimpan : ' . $e->getMessage());
			return redirect()->back();
		}

		Rmt::success('Berhasil menyimpan data');
		return redirect()->back();
	}

	public function deleteKelas(Request $r, $id)
	{
		$rule = DB::table('jadwal_kuliah')->where('kode_kls', $r->nm_kelas)->count();

		if ($rule > 0) {
			Rmt::error('Gagal menghapus, kelas sedang terpakai pada jadwal');
			return redirect()->back();
		}

		Kelas::where('id', $id)->delete();
		Rmt::success('Berhasil menghapus data');
		return redirect()->back();
	}
	/* End Kelas */

	/* Jam Kuliah */
	public function jamkuliah(Request $r)
	{
		$data['jamkul'] = DB::table('jam_kuliah as jk')
			->leftJoin('prodi as pr', 'pr.id_prodi', 'jk.id_prodi')
			->whereIn('jk.id_prodi', Sia::getProdiUser())
			->orderBy('jk.id_prodi')
			->orderBy('jk.jam_masuk', 'asc')
			->get();

		return view('master.jam-kuliah.index', $data);
	}

	public function storeJamkuliah(Request $r)
	{
		$this->validate($r, [
			'prodi' => 'required',
			'jam_masuk' => 'required',
			'jam_keluar' => 'required',
			'ket' => 'required'
		]);

		if ($r->jam_masuk >= $r->jam_keluar) {
			Rmt::error('Gagal menyimpan, Jam masuk harus lebih kecil dari jam keluar');
			return redirect()->back();
		}

		try {

			DB::transaction(function () use ($r) {

				$data = new Jamkuliah;
				$data->id_prodi = $r->prodi;
				$data->jam_masuk = $r->jam_masuk;
				$data->jam_keluar = $r->jam_keluar;
				$data->ket 	= $r->ket;
				$data->save();
			});
		} catch (\Exception $e) {
			Rmt::error('Gagal menyimpan : ' . $e->getMessage());
			return redirect()->back();
		}

		Rmt::success('Berhasil menyimpan data');
		return redirect()->back();
	}

	public function updateJamkuliah(Request $r)
	{
		$this->validate($r, [
			'prodi' => 'required',
			'jam_masuk' => 'required',
			'jam_keluar' => 'required',
			'ket' => 'required'
		]);

		if ($r->jam_masuk >= $r->jam_keluar) {
			Rmt::error('Gagal menyimpan, Jam masuk harus lebih kecil dari jam keluar');
			return redirect()->back();
		}

		try {

			DB::transaction(function () use ($r) {

				$data = Jamkuliah::find($r->id);
				$data->id_prodi = $r->prodi;
				$data->jam_masuk = $r->jam_masuk;
				$data->jam_keluar = $r->jam_keluar;
				$data->ket 	= $r->ket;
				$data->save();
			});
		} catch (\Exception $e) {
			Rmt::error('Gagal menyimpan : ' . $e->getMessage());
			return redirect()->back();
		}

		Rmt::success('Berhasil menyimpan data');
		return redirect()->back();
	}

	public function deleteJamkuliah($id)
	{
		$rule = DB::table('jadwal_kuliah')->where('id_jam', $id)->count();

		if ($rule > 0) {
			Rmt::error('Gagal menghapus, jam sedang terpakai pada jadwal kuliah');
			return redirect()->back();
		}

		Jamkuliah::where('id', $id)->delete();
		Rmt::success('Berhasil menghapus data');
		return redirect()->back();
	}
	/* End */

	/* Ruangan */
	public function ruangan(Request $r)
	{
		$data['ruangan'] = DB::table('ruangan as k')
			->get();

		return view('master.ruangan.index', $data);
	}

	public function storeRuangan(Request $r)
	{
		$this->validate($r, [
			'kode' => 'required|max:3|unique:ruangan,id',
			'nm_ruangan' => 'required'
		]);

		try {
			DB::transaction(function () use ($r) {

				$data = new Ruangan;
				$data->id = $r->kode;
				$data->nm_ruangan = $r->nm_ruangan;
				$data->save();
			});
		} catch (\Exception $e) {
			Rmt::error('Gagal menyimpan : ' . $e->getMessage());
			return redirect()->back();
		}

		Rmt::success('Berhasil menyimpan data');
		return redirect()->back();
	}

	public function updateRuangan(Request $r)
	{
		$this->validate($r, [
			'kode' => 'required|max:3|unique:ruangan,id,' . $r->kode,
			'nm_ruangan' => 'required'
		]);

		try {
			DB::transaction(function () use ($r) {

				$data = Ruangan::find($r->kode);
				$data->nm_ruangan = $r->nm_ruangan;
				$data->save();
			});
		} catch (\Exception $e) {
			Rmt::error('Gagal menyimpan : ' . $e->getMessage());
			return redirect()->back();
		}

		Rmt::success('Berhasil menyimpan data');
		return redirect()->back();
	}

	public function deleteRuangan(Request $r, $id)
	{
		$rule = DB::table('jadwal_kuliah')->where('ruangan', $id)->count();

		if ($rule > 0) {
			Rmt::error('Gagal menghapus, ruangan sedang terpakai pada jadwal');
			return redirect()->back();
		}

		Ruangan::where('id', $id)->delete();
		Rmt::success('Berhasil menghapus data');
		return redirect()->back();
	}
	/* End  */

	/* semester */
	public function semester(Request $r)
	{
		$data['semester'] = Semester::orderBy('id_smt', 'desc')->get();

		return view('master.semester.index', $data);
	}

	public function storeSemester(Request $r)
	{
		$this->validate($r, [
			'id_smt' => 'required|min:5|max:5|unique:semester',
			'nm_semester' => 'required|max:20',
			'ket' => 'required|max:1'
		]);

		try {
			DB::transaction(function () use ($r) {

				$data = new Semester;
				$data->id_smt = $r->id_smt;
				$data->nm_smt = $r->nm_semester;
				$data->smt = $r->ket;
				$data->save();
			});
		} catch (\Exception $e) {
			Rmt::error('Gagal menyimpan : ' . $e->getMessage());
			return redirect()->back();
		}

		Rmt::success('Berhasil menyimpan data');
		return redirect()->back();
	}

	public function updateSemester(Request $r)
	{
		$this->validate($r, [
			'id_smt' => 'required|min:5|max:5',
			'nm_semester' => 'required|max:20',
			'ket' => 'required|max:1'
		]);

		$rule = Semester::where('id_smt', $r->id_smt)
			->where('id_smt', '<>', $r->id_smt)->count();

		if ($rule > 0) {
			Rmt::error('Kode telah ada.');
			return redirect()->back();
		}

		try {
			DB::transaction(function () use ($r) {

				DB::table('semester')->where('id_smt', $r->id_smt)
					->update(['nm_smt' => $r->nm_semester, 'smt' => $r->ket]);
			});
		} catch (\Exception $e) {
			Rmt::error('Gagal menyimpan : ' . $e->getMessage());
			return redirect()->back();
		}

		Rmt::success('Berhasil menyimpan data');
		return redirect()->back();
	}

	public function updateStatusSemester(Request $r)
	{
		$data = ['aktif' => $r->status == 'true' ? '1' : '0'];
		DB::table('semester')->where('id_smt', $r->id)->update($data);
		dd($data);
	}

	public function deleteSemester(Request $r, $id)
	{
		$rule = DB::table('mahasiswa_reg')->where('semester_mulai', $id)->count();

		if ($rule > 0) {
			Rmt::error('Gagal menghapus, semester sedang terpakai');
			return redirect()->back();
		}

		Semester::where('id_smt', $id)->delete();
		Rmt::success('Berhasil menghapus data');
		return redirect()->back();
	}
	/* End  */

	
}
