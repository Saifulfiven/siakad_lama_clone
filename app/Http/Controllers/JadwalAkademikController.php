<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB, Rmt, Sia, Carbon;

class JadwalAkademikController extends Controller
{
    public function index(Request $r)
    {
    	$jadwal = DB::table('jadwal_akademik as ja')
                            ->leftJoin('fakultas as f', 'ja.id_fakultas', 'f.id')
                            ->select('ja.*','f.nm_fakultas');

        if ( !Sia::admin() ) {
            $jadwal->where('id_fakultas', Sia::getFakultasUser());
        }
        
        $data['jadwal'] = $jadwal->get();

    	return view('jadwal-akademik.index', $data);
    }

    public function edit($id)
    {
        $r = DB::table('jadwal_akademik')
                    ->where('id', $id)->first(); ?>

        <form action="<?= route('ja_update') ?>" id="form-jadwal" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $id ?>">

            <div class="table-responsive">
                <table border="0" class="table table-hover table-form">
                    <tr>
                        <td>Awal Pembayaran <span>*</span></td>
                        <td>
                            <input type="date" class="form-control" name="awal_pembayaran" value="<?= $r->awal_pembayaran ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>Akhir Pembayaran <span>*</span></td>
                        <td>
                            <input type="date" class="form-control" name="akhir_pembayaran" value="<?= $r->akhir_pembayaran ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>Awal KRS <span>*</span></td>
                        <td>
                            <input type="date" class="form-control" name="awal_krs" value="<?= $r->awal_krs ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>Akhir KRS <span>*</span></td>
                        <td>
                            <input type="date" class="form-control" name="akhir_krs" value="<?= $r->akhir_krs ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>Awal Kuliah <span>*</span></td>
                        <td>
                            <input type="date" class="form-control" name="awal_kuliah" value="<?= $r->awal_kuliah ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>Status Input Nilai SP <span>*</span></td>
                        <td>
                            <select name="input_nilai_sp" class="form-control">
                                <option value="0" <?= $r->input_nilai_sp == 0 ? 'selected':'' ?>>Tertutup</option>
                                <option value="1" <?= $r->input_nilai_sp == 1 ? 'selected':'' ?>>Terbuka</option>
                        </td>
                    </tr>
                </table>
            </div>
            <hr>
            <button type="submit" id="btn-submit" class="pull-right btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
        </form>
        <?php
    }

    public function update(Request $r)
    {
    	$this->validate($r, [
            'awal_pembayaran' => 'required',
            'akhir_pembayaran' => 'required|date|after_or_equal:awal_pembayaran',
            'awal_krs' => 'required',
    		'akhir_krs' => 'required|date',
            'awal_kuliah' => 'required',
            'input_nilai_sp' => 'required',
    	]);

        $data = 
        [
            'awal_pembayaran' => Carbon::parse($r->awal_pembayaran)->format('Y-m-d'),
            'akhir_pembayaran' => Carbon::parse($r->akhir_pembayaran)->format('Y-m-d'),
            'awal_krs' => Carbon::parse($r->awal_krs)->format('Y-m-d'),
            'akhir_krs' => Carbon::parse($r->akhir_krs)->format('Y-m-d'),
            'awal_kuliah' => Carbon::parse($r->awal_kuliah)->format('Y-m-d'),
            'input_nilai_sp' => $r->input_nilai_sp,
        ];

        $now = date('Y-m-d');
        if ( $data['akhir_krs'] < $data['awal_krs'] ) {
            Rmt::error('Tanggal awal KRS harus lebih kecil dari akhir KRS');
            return redirect()->back();
        }

        DB::table('jadwal_akademik')->where('id', $r->id)->update($data);

    	Rmt::success('Berhasil menyimpan data');
    	return redirect()->back();
    }

}