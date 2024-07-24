<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DaftarIjazah;
use Sia, DB, Response, Rmt, Auth, Session;

class ValidasiPengambilanIjazahController extends Controller
{
    public function index(Request $r)
    {
        if ( $r->pencarian ) {
            if ( !empty($r->cari) ) {
                Session::put('ijazah.cari',$r->cari);
            } else {
                Session::pull('ijazah.cari');
            }

            return redirect()->route('val_ijazah');
        }

        $mahasiswa = DB::table('pendaftaran_ijazah as pi')
                                ->leftJoin('mahasiswa_reg as m1', 'pi.id_mhs_reg', 'm1.id')
                                ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                                ->leftJoin('prodi as pr', 'm1.id_prodi', 'pr.id_prodi')
                                ->select('pi.*','m1.nim','m1.bebas_pembayaran', 'm1.bebas_pustaka','m1.bebas_skripsi','m2.nm_mhs','m1.id_jenis_keluar', 'pr.jenjang', 'pr.nm_prodi')
                                ->orderBy('pi.created_at','desc');

        $this->filter($mahasiswa);

        $data['mahasiswa'] = $mahasiswa->paginate(20);

        return view('validasi-ijazah.index', $data);
    }

    public function filter($query)
    {

        if ( Session::has('ijazah.prodi') ) {
            $query->where('m1.id_prodi', Session::get('ijazah.prodi'));
        } else {
            $query->whereIn('m1.id_prodi', Sia::getProdiUser());
        }

        $level = Auth::user()->level;

        if ( Session::has('ijazah.status') && $level == 'jurusan' ) {
            
            $query->where('m1.bebas_skripsi', Session::get('ijazah.status'));

        } elseif ( Session::has('ijazah.status') && $level == 'pustakawan' ) {

            $query->where('m1.bebas_pustaka', Session::get('ijazah.status'));

        } elseif ( Session::has('ijazah.status') && $level == 'keuangan' ) {

            $query->where('m1.bebas_pembayaran', Session::get('ijazah.status'));

        }

        if ( Session::has('ijazah.cari') ) {
            $query->where(function($q) {
                $q->where('m1.nim','LIKE','%'.Session::get('ijazah.cari').'%')
                    ->orWhere('m2.nm_mhs', 'LIKE', '%'.Session::get('ijazah.cari').'%');
            });
        }
    }

    public function update(Request $r)
    {

        try {

            $level = Auth::user()->level;

            if ( $level == 'jurusan') {
                
                $data = [
                    'bebas_skripsi' => $r->val
                ];

            } elseif ( $level == 'pustakawan' ) {
                $data = [
                    'bebas_pustaka' => $r->val
                ];
            } elseif ( $level == 'keuangan' ) {
                $data = [
                    'bebas_pembayaran' => $r->val
                ];
            }

            DB::table('mahasiswa_reg')
                ->where('id', $r->mhs)
                ->update($data);

            Rmt::success('Berhasil mengubah status');

            return redirect()->back();

        } catch( \Exception $e ) {

            Rmt::error($e->getMessage());
            return redirect()->back();

        }
    }


    public function download(Request $r)
    {

        $path = config('app.syarat-ijazah').'/'.Sia::sessionMhs('nim');
        $pathToFile = $path.'/'.$r->file;
        
        if ( file_exists($pathToFile) ) {
            return Response::download($pathToFile, $r->file);
        } else {
            echo "<center><h4>File tidak ditemukan</h4></center>";
        }

    }
    
}