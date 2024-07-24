<?php

namespace App\Http\Controllers\mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Bimbinganmhs, App\Bimbingandetail, App\Ujianakhir, App\Mahasiswareg, App\Dosen, App\Seminar, App\SeminarValidasi;
use Rmt, Carbon, Response, DB, Session;

class BimbinganController extends Controller
{
    protected $view;

    public function __construct()
    {
        $this->view = 'mobile';
    }

    public function index(Request $r, $id_dosen, $id_mhs_reg, $id_smt)
    {

        $data['menguji'] = DB::table('penguji as p')
            ->leftJoin('dosen as d', 'p.id_dosen', 'd.id')
            ->select('p.*')
            ->where('p.id_mhs_reg', $id_mhs_reg)
            ->where('p.id_dosen', $id_dosen)
            ->where('p.id_smt', $id_smt)
            ->groupBy('p.jenis')
            ->get()
            ->toArray();


        if ( $r->jenis ) {
            Session::put('bim.jenis', $r->jenis);
        } else {
            Session::put('bim.jenis', $data['menguji'][0]->jenis);
        }

        $jenis = Session::get('bim.jenis');

        // Insert jika belum ada di tabel ujian akhir
        Ujianakhir::firstOrCreate([
            'id_smt' => $id_smt,
            'id_mhs_reg' => $id_mhs_reg,
            'jenis' => $jenis,
        ]);

        $data['id_smt'] = $id_smt;

        $data['mhs'] = Mahasiswareg::findOrFail($id_mhs_reg);

        // Ambil data pembimbing dan penguji dari table penguji & ujian_akhir
        $data['bimbingan'] = Rmt::bimbinganMhs($id_smt, $id_mhs_reg, $jenis);

        $cek_bimbingan = Bimbinganmhs::where('id_mhs_reg', $id_mhs_reg)
                            ->where('jenis', $jenis)
                            ->where('id_smt', $id_smt)
                            ->count();

        // Insert data ke bimbingan
        if ( empty($cek_bimbingan) ) {
            $bimb = new Bimbinganmhs;
            $bimb->id_mhs_reg = $id_mhs_reg;
            $bimb->jenis = $jenis;
            $bimb->id_smt = $id_smt;
            $bimb->save();
        }

        // Ambil 1 data bimbingan_mhs dari tabel bimbingan_mhs
        $data['data_bim'] = Bimbinganmhs::where('id_mhs_reg', $id_mhs_reg)
                            ->where('jenis', $jenis)
                            ->where('id_smt', $id_smt)
                            ->first();

        $data['id_dosen'] = $id_dosen;

        return view($this->view.'.bimbingan-dosen.index', $data);
    }

    public function addKomentar($id, $id_mhs_reg, $id_dosen)
    {
        $data['mhs'] = Mahasiswareg::findOrFail($id_mhs_reg);
        $data['bim'] = Bimbinganmhs::findOrFail($id);
        $data['id_dosen'] = $id_dosen;
        
        return view($this->view.'.bimbingan-dosen.add', $data);
    }

    public function storeKomentar(Request $r)
    {

        $this->validate($r, [
            'tanggal' => 'required',
            'sub_pokok_bahasan' => 'required',
            'komentar' => 'required'
        ]);

        try{

            $data = new Bimbingandetail;
            $data->id_bimbingan_mhs = $r->id_bimbingan;
            $data->id_dosen = $r->id_dosen;
            $data->jabatan_pembimbing = $r->jabatan;
            $data->sub_bahasan = $r->sub_pokok_bahasan;
            $data->komentar = $r->komentar;
            $data->tgl_bimbingan = Carbon::parse($r->tanggal)->format('Y-m-d');

            if ( $r->hasFile('file') ) {
                $namaFile = $r->file->getClientOriginalName();
                $fileName   = time().'-'.$namaFile;
                $path   = config('app.file-bimbingan').'/'.$r->jenis;
                $upload = $r->file->move($path, $fileName);

                $data->file = $fileName;
            }

            $data->save();

        } catch( \Exception $e ) {
            return Response::json([$e->getMessage()], 422);
        }
    }

    public function editKomentar($id, $id_dosen)
    {
        $data['bim'] = Bimbingandetail::where('id', $id)
                        ->where('id_dosen', $id_dosen)
                        ->first();

        $data['id_dosen'] = $id_dosen;

        return view($this->view.'.bimbingan-dosen.edit', $data);
    }


    public function deleteLampiran(Request $r, $id)
    {
        $bim = Bimbingandetail::where('id', $id)
                ->where('id_dosen', $r->id_dosen)
                ->first();

        $path = $path   = config('app.file-bimbingan').'/'.$r->jenis;
        if ( file_exists($path.'/'.$bim->file) ) {
            unlink($path.'/'.$bim->file);
        }

        $bim->file = '';
        $bim->save();

        Rmt::success('Berhasil menghapus file');

        return redirect()->back();
    }

    public function updateKomentar(Request $r)
    {
        $this->validate($r, [
            'tanggal' => 'required',
            'sub_pokok_bahasan' => 'required',
            'komentar' => 'required'
        ]);

        try{

            $data = Bimbingandetail::findOrFail($r->id_bimbingan_detail);
            $data->sub_bahasan = $r->sub_pokok_bahasan;
            $data->komentar = $r->komentar;
            $data->tgl_bimbingan = Carbon::parse($r->tanggal)->format('Y-m-d');

            if ( $r->hasFile('file') ) {
                $namaFile = $r->file->getClientOriginalName();
                $fileName   = time().'-'.$namaFile;
                $path   = config('app.file-bimbingan').'/'.$r->jenis;
                $upload = $r->file->move($path, $fileName);

                $data->file = $fileName;
            }

            $data->save();

            Rmt::success('Berhasil menyimpan data');
            return Response::json(['Berhasil menyimpan data']);

        } catch( \Exception $e ) {
            return Response::json([$e->getMessage()], 422);
        }
    }

    public function delete($id, $id_dosen, $jenis)
    {
        try {

            $data = Bimbingandetail::where('id', $id)
                    ->where('id_dosen', $id_dosen)
                    ->first();

            if ( !empty($data) && !empty($data->file) ) {

                $path = config('app.file-bimbingan').'/'.$jenis;
                if ( file_exists($path.'/'.$data->file) ) {
                    unlink($path.'/'.$data->file);
                }

            }

            $data->delete();

            Rmt::success('Berhasil menghapus data');
            return redirect()->back();
        
        } catch ( \Exception $e ) {
            Rmt::error('Gagal menghapus data');
            return redirect()->back();
        }


    }

    public function download(Request $r)
    {
        $file = Bimbinganmhs::where('id_mhs_reg', $r->id_mhs_reg)
                ->where('id', $r->id)->first();

        if ( !empty($file) ) { 
            $path = config('app.file-bimbingan').'/'.$r->jenis;
            $pathToFile = $path.'/'.$file->file;
            
            if ( file_exists($pathToFile) ) {
                return Response::download($pathToFile, $file->file);
            } else {
                echo "<center><h4>File tidak ditemukan</h4></center>";
            }
        } else {
            echo "<center><h4>File tidak ditemukan</h4></center>";
        }
    }

    public function lampiran(Request $r)
    {
        $file = Bimbingandetail::where('id_bimbingan_mhs', $r->id_bim)
                ->where('id', $r->id)->first();


        if ( !empty($file) ) { 
            $path = config('app.file-bimbingan').'/'.$r->jenis;
            $pathToFile = $path.'/'.$file->file;
            
            if ( file_exists($pathToFile) ) {
                return Response::download($pathToFile, $file->file);
            } else {
                echo "<center><h4>File tidak ditemukan</h4></center>";
            }
        } else {
            echo "<center><h4>File tidak ditemukan</h4></center>";
        }
    }

    public function selesai(Request $r)
    {
        try {

            DB::beginTransaction();

            $data = Bimbinganmhs::findOrFail($r->id);

            if ( $r->jabatan == 'KETUA' ) {
                $data->pembimbing_1 = $r->value;
            }

            if ( $r->jabatan == 'SEKRETARIS' ) {
                $data->pembimbing_2 = $r->value;
            }

            $data->save();

            if ( $r->value == 1 && $data->pembimbing_1 == 1 && $data->pembimbing_2 == 1 ) {
                
                $id_seminar = $this->insertSeminar($r->id_mhs_reg, $r->jenis, $r->id_smt);

                $this->insertUjianAkhir($r->id_mhs_reg, $r->jenis, $r->id_smt);

                $this->cekPembayaran($r->id_mhs_reg, $r->jenis, $id_seminar);

            }


            DB::commit();

        } catch( \Exception $e ) {

            DB::rollback();
            Rmt::error($e->getMessage());
        }

        return redirect()->back();
    }

    private function insertSeminar($id_mhs_reg, $jenis, $id_smt)
    {
        try {

            $data = new Seminar;

            // Simpan data pendaftaran seminar
            $data->id_mhs_reg = $id_mhs_reg;
            $data->id_smt = $id_smt;
            $data->jenis = $jenis;
            $data->save();

            return $data->id;

        } catch( \Exception $e ) {
            abort(422, $e->getMessage());
        }
    }

    private function insertUjianAkhir($id_mhs_reg, $jenis, $id_smt)
    {
        try {

            $data_ua = [
                    'id_smt' => $id_smt,
                    'jenis' => $jenis
                ];

            $a = Ujianakhir::updateOrCreate([
                'id_smt' => $id_smt,
                'id_mhs_reg' => $id_mhs_reg,
                'jenis' => $jenis,
            ], $data_ua);

        } catch( \Exception $e ) {
            abort(422, $e->getMessage());
        }
    }

    private function cekPembayaran($id_mhs_reg, $jenis, $id_seminar)
    {
        // Untuk otomatis menyetujui pembayaran seminar
        // Cek di table pembayaran dengan jenis (get table jenis_pembayaran)

        // List jenis pembayaran S1 di database
        // id: 4 => P
        // id: 5 => H
        // id: 6 => S
        
        // List jenis pembayaran S2 di database
        // id: 8 => P
        // id: 9 => H
        // id: 10 => S

        try {

            // Dari database jenis_pembayaran
            $s1 = [
                'P' => 4,
                'H' => 5,
                'S' => 6
            ];

            $s2 = [
                'P' => 8,
                'H' => 9,
                'S' => 10
            ];

            $mhs = Mahasiswareg::select('id_prodi')->where('id', $id_mhs_reg)->first();

            $jenis_bayar = $mhs->id_prodi == 61101 ? $s2[$jenis] : $s1[$jenis];
            
            $pembayaran = DB::table('pembayaran')
                            ->where('id_mhs_reg', $id_mhs_reg)
                            ->where('id_jns_pembayaran', $jenis_bayar)
                            ->count();

            if ( $pembayaran > 0 ) {
                DB::table('seminar_pendaftaran')
                    ->where('id', $id_seminar)
                    ->update([
                        'validasi_bauk' => '1'
                    ]);
            }


        } catch( \Exception $e ) {
            abort(422, $e->getMessage());
        }
    }
}
