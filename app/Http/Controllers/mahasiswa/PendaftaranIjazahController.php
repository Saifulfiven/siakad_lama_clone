<?php

namespace App\Http\Controllers\mahasiswa;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DaftarIjazah;
use Sia, DB, Response, Rmt;

class PendaftaranIjazahController extends Controller
{
    public function index(Request $r)
    {

        $mahasiswa = DB::table('pendaftaran_ijazah as pi')
                                ->leftJoin('mahasiswa_reg as m1', 'pi.id_mhs_reg', 'm1.id')
                                ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                                ->select('pi.*','m1.nim','m1.bebas_pembayaran', 'm1.bebas_pustaka','m1.bebas_skripsi','m2.nm_mhs','m1.id_jenis_keluar')
                                ->where('pi.id_mhs_reg', Sia::sessionMhs())
                                ->first();

        $data['mahasiswa'] = $mahasiswa;

        if ( empty($mahasiswa) && Sia::sessionMhs('jenis_keluar') == 1 ) {
            
            $newData = new DaftarIjazah;
            $newData->id_mhs_reg = Sia::sessionMhs();
            $newData->save();

            $data['mahasiswa'] = DB::table('pendaftaran_ijazah as pi')
                                ->leftJoin('mahasiswa_reg as m1', 'pi.id_mhs_reg', 'm1.id')
                                ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                                ->select('pi.*','m1.nim','m1.bebas_pembayaran', 'm1.bebas_pustaka','m1.bebas_skripsi','m2.nm_mhs','m1.id_jenis_keluar')
                                ->where('pi.id_mhs_reg', Sia::sessionMhs())
                                ->first();
        }


        return view('mahasiswa-member.pendaftaran-ijazah.index', $data);
    }

    public function store(Request $r)
    {
        $this->validate($r, [
            'file' => 'max:10240',
        ]);

        try {

            DB::beginTransaction();

            $id_mhs_reg = Sia::sessionMhs();

            $cek = DaftarIjazah::where('id_mhs_reg', $id_mhs_reg)->count();

            switch( $r->jenis ) {
                case "S":
                    
                    $file = $this->upload($r->file, $r->jenis);
                    $data = ['skripsi' => $file];

                break;

                case "T":

                    $file = $this->upload($r->file, $r->jenis);
                    $data = ['turnitin' => $file];

                break;

            }
            
            DaftarIjazah::updateOrCreate([ 
                    'id_mhs_reg' => Sia::sessionMhs()
                ], $data);

            DB::commit();

            Rmt::success('Upload Berhasil');

            return Response::json(['OK']);

        } catch( \Exception $e ) {

            DB::rollback();
            return Response::json([$e->getMessage()], 422);

        }
    }

    private function upload($file, $jenis)
    {
        
        $ekstArr = ['pdf'];
        $ekstensi = $file->getClientOriginalExtension();

        if ( !in_array($ekstensi, $ekstArr) ) {
            abort(422, 'Jenis file yang diperbolehkan adalah ('.implode(',', $ekstArr).')');
        }

        $nama_nim   = Sia::sessionMhs('nim') .'-'.str_slug(Sia::sessionMhs('nama'));

        $fileName   = $jenis.' - '.$nama_nim.'.'.strtolower($ekstensi);
        $path   = config('app.syarat-ijazah').'/'.Sia::sessionMhs('nim');
        $upload = $file->move($path, $fileName);

        return $fileName;
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