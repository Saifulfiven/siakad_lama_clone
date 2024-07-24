<?php

namespace App\Http\Controllers\dosen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Bimbinganmhs, App\Bimbingandetail, App\Ujianakhir, App\Mahasiswareg, App\Dosen, App\Seminar, App\SeminarValidasi;
use Sia, Rmt, Session, Carbon, Response, DB;

class BimbinganController extends Controller
{
	protected $view;

	public function __construct()
	{
		$this->view = 'dsn.bimbingan';
	}

	private function filterAwal()
	{
		if ( !Session::has('bim.smt') ) {
    		Session::put('bim.smt', Sia::sessionPeriode());
    	}
	}

    public function index(Request $r)
    {
    	$this->filterAwal();

    	$id_smt = Session::get('bim.smt');

    	$bimbingan = Rmt::bimbinganDosen($id_smt, Sia::sessionDsn());

        if ( Session::has('bim.cari') ) {

            $cari = Session::get('bim.cari');
            $bimbingan->where(function($q)use($cari){
                $q->where('m1.nim', 'LIKE', '%'.$cari.'%')
                    ->orWhere('m2.nm_mhs', 'LIKE', '%'.$cari.'%');
            });
        }

        $data['bimbingan'] = $bimbingan->paginate(20);

    	return view($this->view.'.index', $data);
    }

    public function detail(Request $r, $id_mhs_reg, $id_smt)
    {
        if ( !Session::has('bim.smt') ) {
            Session::put('bim.smt', $id_smt);
        }

        $data['menguji'] = DB::table('penguji as p')
            ->leftJoin('dosen as d', 'p.id_dosen', 'd.id')
            ->select('p.*')
            ->where('p.id_mhs_reg', $id_mhs_reg)
            ->where('p.id_dosen', Sia::sessionDsn())
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

        return view($this->view.'.detail', $data);
    }

    public function addKomentar($id, $id_mhs_reg)
    {
        $data['mhs'] = Mahasiswareg::findOrFail($id_mhs_reg);
        $data['bim'] = Bimbinganmhs::findOrFail($id);
        
        if ( !Session::has('bim.jenis') ) {
            return redirect()->back();
        }

        return view($this->view.'.add', $data);
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
            $data->id_dosen = Sia::sessionDsn();
            $data->jabatan_pembimbing = $r->jabatan;
            $data->sub_bahasan = $r->sub_pokok_bahasan;
            $data->komentar = $r->komentar;
            $data->tgl_bimbingan = Carbon::parse($r->tanggal)->format('Y-m-d');

            if ( $r->hasFile('file') ) {
                $namaFile = $r->file->getClientOriginalName();
                $fileName   = time().'-'.$namaFile;
                $path   = config('app.file-bimbingan').'/'.Session::get('bim.jenis');
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

    public function editKomentar($id)
    {
        $data['bim'] = Bimbingandetail::where('id', $id)
                        ->where('id_dosen', Sia::sessionDsn())
                        ->first();

        if ( empty($data['bim']) ) {
            return view('errors.404');
        }

        return view($this->view.'.edit', $data);
    }

    public function deleteLampiran($id)
    {
        $bim = Bimbingandetail::where('id', $id)
                ->where('id_dosen', Sia::sessionDsn())
                ->first();

        if ( empty($bim) ) {
            return view('errors.404');
        }

        $path = $path   = config('app.file-bimbingan').'/'.Session::get('bim.jenis');
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
                $path   = config('app.file-bimbingan').'/'.Session::get('bim.jenis');
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

    public function delete($id)
    {
        $data = Bimbingandetail::where('id', $id)
                ->where('id_dosen', Sia::sessionDsn())
                ->first();

        if ( empty($data) ) {
            return view('errors.404');
        }

        $path = $path   = config('app.file-bimbingan').'/'.Session::get('bim.jenis');
        if ( file_exists($path.'/'.$data->file) ) {
            unlink($path.'/'.$data->file);
        }

        $data->delete();

        Rmt::success('Berhasil menghapus data');

        return redirect()->back();

    }

    public function cari(Request $r)
    {
        if ( !empty($r->cari) ) {
            Session::put('bim.cari',$r->cari);
        } else {
            Session::pull('bim.cari');
        }

        return redirect(route('dsn_bim'));
    }

    public function setFilter(Request $r)
    {
        if ( !empty($r->modul) ) {

            if ( $r->val == 'all' ) {
                Session::pull('bim.'.$r->modul);
            } else {
                Session::put('bim.'.$r->modul,$r->val);
            }
        }

        if ( $r->remove ) {
            Session::pull('bim');
        }

        if ( $r->go ) {
            return redirect()->back();
        }

        return redirect(route('dsn_bim'));
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

            if ( Sia::sessionDsn() == $r->id_dosen ) {

                $data = Bimbinganmhs::findOrFail($r->id);

                if ( $r->jabatan == 'KETUA' ) {
                    $data->pembimbing_1 = $r->value;
                }

                if ( $r->jabatan == 'SEKRETARIS' ) {
                    $data->pembimbing_2 = $r->value;
                }

                $data->save();

                if ( $r->value == 1 && $data->pembimbing_1 == 1 && $data->pembimbing_2 == 1 ) {
                    
                    $id_seminar = $this->insertSeminar($r->id_mhs_reg, Session::get('bim.jenis'));

                    $this->insertUjianAkhir($r->id_mhs_reg, Session::get('bim.jenis'));

                    $this->cekPembayaran($r->id_mhs_reg, Session::get('bim.jenis'), $id_seminar);

                }


                DB::commit();
            }

        } catch( \Exception $e ) {

            DB::rollback();
            Rmt::error($e->getMessage());
        }

        return redirect()->back();
    }

    private function insertSeminar($id_mhs_reg, $jenis)
    {
        try {

            $data = new Seminar;

            // Simpan data pendaftaran seminar
            $data->id_mhs_reg = $id_mhs_reg;
            $data->id_smt = Session::get('bim.smt');
            $data->jenis = $jenis;
            $data->save();

            return $data->id;

        } catch( \Exception $e ) {
            abort(422, $e->getMessage());
        }
    }

    private function insertUjianAkhir($id_mhs_reg, $jenis)
    {
        try {

            $data_ua = [
                    'id_smt' => Session::get('bim.smt'),
                    'jenis' => $jenis
                ];

            $a = Ujianakhir::updateOrCreate([
                'id_smt' => Session::get('bim.smt'),
                'id_mhs_reg' => $id_mhs_reg,
                'jenis' => $jenis,
            ], $data_ua);

        } catch( \Exception $e ) {
            abort(422, $e->getMessage());
        }
    }

    private function getPembimbing($id_mhs_reg, $jenis)
    {
        $ketua = Sia::penguji($id_mhs_reg, $jenis, 'KETUA');
        $sekretaris = Sia::penguji($id_mhs_reg, $jenis, 'SEKRETARIS');

        $result = [$ketua->id, $sekretaris->id];
        
        return $result;
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
