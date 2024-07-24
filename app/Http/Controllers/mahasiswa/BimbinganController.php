<?php

namespace App\Http\Controllers\mahasiswa;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Bimbinganmhs, App\Bimbingandetail, App\Ujianakhir, App\Dosen, App\Mahasiswareg;
use Sia, Rmt, Session, Response, QrCode;

class BimbinganController extends Controller
{
	protected $view;

	public function __construct()
	{
		$this->view = 'mahasiswa-member.bimbingan';
	}

	private function filterAwal()
	{
		if ( !Session::has('bim.smt') ) {
    		Session::put('bim.smt', Sia::sessionPeriode());
    	}

    	if ( !Session::has('bim.jenis') ) {
    		Session::put('bim.jenis', 'P');
    	}

	}

    public function index(Request $r)
    {
    	$this->filterAwal();
    	$id_smt = Session::get('bim.smt');
    	$jenis = Session::get('bim.jenis');

        // Ambil data pembimbing dan penguji dari table penguji & ujian_akhir
    	$data['bimbingan'] = Rmt::bimbinganMhs($id_smt, Sia::sessionMhs(), $jenis);

        $cek_bimbingan = Bimbinganmhs::where('id_mhs_reg', Sia::sessionMhs())
                            ->where('jenis', $jenis)
                            ->where('id_smt', $id_smt)
                            ->count();

        // Insert data ke bimbingan
        if ( empty($cek_bimbingan) ) {
            $bimb = new Bimbinganmhs;
            $bimb->id_mhs_reg = Sia::sessionMhs();
            $bimb->jenis = Session::get('bim.jenis');
            $bimb->id_smt = Session::get('bim.smt');
            $bimb->save();
        }

        // Ambil 1 data bimbingan_mhs dari tabel bimbingan_mhs
        $data['data_bim'] = Bimbinganmhs::where('id_mhs_reg', Sia::sessionMhs())
                            ->where('jenis', $jenis)
                            ->where('id_smt', $id_smt)
                            ->first();

    	return view($this->view.'.index', $data);
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

        return redirect(route('mhs_bim'));
    }

    public function uploadFile(Request $r)
    {
        try {

            if ( $r->hasFile('file') ) {

                $versi = 1;
                if ( !empty($r->id) ) {
                    $data = Bimbinganmhs::find($r->id);
                    $versi = $data->versi + 1;
                }

                $ekstArr = ['docx','pdf'];
                $ekstensi = $r->file->getClientOriginalExtension();

                if ( !in_array($ekstensi, $ekstArr) ) {
                    return Response::json(['Jenis file yang diperbolehkan adalah ('.implode(',', $ekstArr).')'], 422);
                }

                $awalan     = Sia::sessionMhs('prodi') == 61101 ? 'tesis':'skripsi';
                $nama_nim   = Sia::sessionMhs('nim') .'-'.str_slug(Sia::sessionMhs('nama'));

                $fileName   = $awalan.'-'.$nama_nim.'-versi-'.$versi.'.'.strtolower($ekstensi);
                $path   = config('app.file-bimbingan').'/'.Session::get('bim.jenis');
                $upload = $r->file->move($path, $fileName);

                if ( !empty($r->id) ) {
                    
                    $data->file = $fileName;
                    $data->link = '';
                    $data->versi = $data->versi + 1;
                    $data->save();

                } else {

                    $data = new Bimbinganmhs;
                    $data->id_mhs_reg = Sia::sessionMhs();
                    $data->jenis = Session::get('bim.jenis');
                    $data->id_smt = Session::get('bim.smt');
                    $data->file = $fileName;
                    $data->save();
                
                }

                Rmt::success('Berhasil mengupload file');

                return Response::json(['OK']);
            
            } else {
                return Response::json(['File tidak terbaca, mohon ulangi lagi'], 422);
            }

        } catch( \Exception $e ) {
            return Response::json([$e->getMessage()], 422);
        }
    }

    public function storeLink(Request $r)
    {
        $this->validate($r, [
            'link' => 'required'
        ]);

        $data = Bimbinganmhs::find($r->id);

        if ( !empty($r->id) ) {
                    
            $data->link = $r->link;
            $data->file = '';
            $data->versi = $data->versi + 1;
            $data->save();

        } else {

            $data = new Bimbinganmhs;
            $data->id_mhs_reg = Sia::sessionMhs();
            $data->jenis = Session::get('bim.jenis');
            $data->id_smt = Session::get('bim.smt');
            $data->link = $r->link;
            $data->save();
        
        }

        Rmt::success('Berhasil menyimpan link');
    }

    public function download(Request $r)
    {
        $file = Bimbinganmhs::where('id_mhs_reg', Sia::sessionMhs())
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

    public function cetak(Request $r, $id)
    {
        $data['mhs'] = Mahasiswareg::findOrFail(Sia::sessionMhs());

        $data['bimbingan'] = Bimbingandetail::where('id_bimbingan_mhs', $id)
                            ->where('jabatan_pembimbing', $r->jb)
                            ->where('id_dosen', $r->dsn)
                            ->get();
        

        $data['dsn'] = Dosen::findOrFail($r->dsn);

        $data['jabatan'] = $r->jb == 'KETUA' ? 'Pembimbing I' : 'Pembimbing II';

        $qr = 'BIMBINGAN '.Sia::sessionMhs('nim').','.Sia::sessionMhs('nama').','.Sia::sessionPeriode('nama');
            
        QrCode::generate($qr, storage_path().'/qr-code/bimbingan-'.Sia::sessionMhs('nim').'.svg');
    
        return view($this->view.'.cetak', $data);
    }
}
