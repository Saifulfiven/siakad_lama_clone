<?php

namespace App\Http\Controllers\mahasiswa;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Sia, Rmt, DB, Response, Auth, Session, QrCode, Image;
use App\Mahasiswa, App\Mahasiswareg, App\konsentrasiPps;
use App\Http\Controllers\MahasiswaDokumen;
use App\InformasiModels\Gbpp;

class MhsController extends Controller
{
    use MahasiswaDokumen;
    
    public function profil(Request $r)
    {
        $id = Auth::user()->mhs->id;

        $data['agama'] = DB::table('agama')->get();
        $data['jenisTinggal'] = DB::table('jenis_tinggal')->get();
        $data['alatTranspor'] = DB::table('alat_transpor')->get();
        $data['alatTranspor'] = DB::table('alat_transpor')->get();
        $data['jnsPendaftaran'] = DB::table('jenis_pendaftaran')->get();
        $data['jalurMasuk'] = DB::table('jalur_masuk')->get();
        $data['prodi'] = DB::table('prodi')->orderBy('jenjang')->get();
        $data['penghasilan'] = DB::table('penghasilan')->get();
        $data['pekerjaan'] = DB::table('pekerjaan')->get();
        $data['pdk'] = DB::table('pendidikan')->get();
        $data['infoNobel'] = DB::table('info_nobel')->get();
        $data['mhs'] = Mahasiswa::where('id',$id)->first();


        if ( empty($data['mhs']) ) {
            return redirect(route('beranda'));
        }
        $data['user'] = \App\User::find(Auth::user()->id);

    	return view('mahasiswa-member.profile', $data);
    }

    public function rps(Request $r){
      if ( empty($r->prodi) ) {
          return redirect()->route('dsn_rps',['prodi' => 61101]);
      }
      
      $data['rps'] = Gbpp::where('prodi', $r->prodi)->orderBy('judul','asc')->get();
  
      return view('dsn.rps.index', $data);
    }

    public function updateProfil(Request $r)
    {

        $this->validate($r, [
            'nik'   => 'required|unique:mahasiswa,id,'.$r->id.'|max:16',
            'kewarganegaraan'   => 'required',
            'kelurahan' => 'required',
            'kecamatan' => 'required',
            'hp' => 'required|numeric',
            'rt' => 'numeric',
            'rw' => 'numeric',
            'npwp' => 'numeric',
            'nisn' => 'numeric',
            'pos' => 'numeric',
            'hp_ibu' => 'numeric',
            'hp_ayah' => 'numeric',
            'hp_wali' => 'numeric',
        ]);

        try {
            DB::transaction(function ()use($r,&$response) {
                $mhs = Mahasiswa::find($r->id);
                $mhs->nik = $r->nik;
                $mhs->nisn = $r->nisn;
                $mhs->npwp = $r->npwp;
                $mhs->alamat = $r->alamat;
                $mhs->dusun = $r->dusun;
                $mhs->des_kel = $r->kelurahan;
                $mhs->rt = $r->rt;
                $mhs->rw = $r->rw;
                $mhs->id_wil = $r->kecamatan;
                $mhs->pos = $r->pos;
                $mhs->hp = $r->hp;
                $mhs->email = $r->email;
                $mhs->kewarganegaraan = $r->kewarganegaraan;
                $mhs->nm_sekolah = $r->nm_sekolah;
                $mhs->tahun_lulus_sekolah = $r->thn_lulus_sekolah;
                $mhs->nik_ibu = $r->nik_ibu;
                $mhs->tgl_lahir_ibu = empty($r->tgl_lahir_ibu) ? NULL : Rmt::formatTgl($r->tgl_lahir_ibu,'Y-m-d');
                $mhs->id_pdk_ibu = empty($r->pdk_ibu) ? NULL : $r->pdk_ibu;
                $mhs->id_pekerjaan_ibu = empty($r->pekerjaan_ibu) ? NULL : $r->pekerjaan_ibu;
                $mhs->id_penghasilan_ibu = empty($r->penghasilan_ibu) ? NULL : $r->penghasilan_ibu;
                $mhs->hp_ibu = $r->hp_ibu;
                $mhs->nik_ayah = $r->nik_ayah;
                $mhs->nm_ayah = $r->nama_ayah;
                $mhs->tgl_lahir_ayah = empty($r->tgl_lahir_ayah) ? NULL : Rmt::formatTgl($r->tgl_lahir_ayah,'Y-m-d');
                $mhs->id_pdk_ayah = empty($r->pdk_ayah) ? NULL : $r->pdk_ayah;
                $mhs->id_pekerjaan_ayah = empty($r->pekerjaan_ayah) ? NULL : $r->pekerjaan_ayah;
                $mhs->hp_ayah = $r->hp_ayah;
                $mhs->id_penghasilan_ayah = empty($r->penghasilan_ayah) ? NULL : $r->penghasilan_ayah;
                $mhs->nm_wali = $r->nama_wali;
                $mhs->tgl_lahir_wali = empty($r->tgl_lahir_wali) ? NULL : Rmt::formatTgl($r->tgl_lahir_wali,'Y-m-d');
                $mhs->id_pdk_wali = empty($r->pdk_wali) ? NULL : $r->pdk_wali;
                $mhs->id_pekerjaan_wali = empty($r->pekerjaan_wali) ? NULL : $r->pekerjaan_wali;
                $mhs->id_penghasilan_wali = empty($r->penghasilan_wali) ? NULL : $r->penghasilan_wali;
                $mhs->hp_wali = $r->hp_wali;
                $mhs->jenis_tinggal = empty($r->jenis_tinggal) ? NULL : $r->jenis_tinggal;
                $mhs->alat_transpor = empty($r->alat_transpor) ? NULL : $r->alat_transpor;
                $mhs->save();

                Rmt::Success('Berhasil menyimpan data');
                $response = ['error' => 0, 'msg' => 'sukses'];
            });
        } catch(\Exception $e) {
            $response = ['error' => 1, 'msg' => $e->getMessage()];
        }

        Rmt::success('Berhasil menyimpan data');
        return Response::json($response,200);
    }

    public function updateFoto(Request $r)
    {
        $this->validate($r, [
            'foto' => 'image|mimes:jpeg,png,jpg,gif,svg|max:500',
        ]);

        $whitelist = array('jpg', 'jpeg', 'png', 'gif', 'svg');

        if ( $r->hasFile('foto') ) {
            $extension = $r->foto->getClientOriginalExtension();
            $imageName = Sia::sessionMhs('nim') .'.'.$extension;
            
            if (!in_array($extension, $whitelist)) {
                $error = 'Type file salah. Pastikan anda mengupload file '.implode(',', $whitelist);
                return Response::json(['error'  => 1,'msg' => $error]);
            } else {
                $path = config('app.foto-mhs');
                $r->foto->move($path, $imageName);
                // Generate thumbnail
                $img = Image::make($path.'/'.$imageName);
                $img->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($path.'/thumb/'.$imageName);

                $mhs = Mahasiswa::find(Sia::SessionMhs('id_mhs'));
                $mhs->foto_mahasiswa = $imageName;
                $mhs->save();
                Rmt::success('Berhasil mengubah foto');
            }

        }

    }

    public function updateAkun(Request $r)
    {

        $this->validate($r, [
            'username' => 'required|unique:users,id,'.$r->id,
            'password' => 'min:6'
        ]);

        if ( empty($r->username) ) {
            return redirect(route('mhs_profil'));
        }

        try {
            DB::transaction(function()use($r){

                $data = \App\User::find(Auth::user()->id);
                if ( empty($data) ) {
                    return redirect(route('mhs_profil'));
                }

                $data->username = $r->username;

                if ( !empty($r->password) ) {
                    $data->password = bcrypt($r->password);
                }
                
                $data->save();

            });
        } catch( \Exception $e) {
            return Response::json(['error'=> $e->getMessage()], 403);
        }

        return Response::json(['error' => 0, 'msg' => 1]);

    }

    /* Jadwal Kuliah */
        public function jadwalKuliah(Request $r)
        {
            $id_reg_pd = Sia::sessionMhs('id_mhs_reg');

            if ( !empty($r->ubah_jenis) ) {
                Session::put('jeniskrs_in_jdk', $r->ubah_jenis);
            } else {
                Session::put('jeniskrs_in_jdk', 1);
            }

            $query = Sia::jadwalKuliahMahasiswa($id_reg_pd, Session::get('jeniskrs_in_jdk'));
            $data['jadwal'] = $query->where('jdk.id_smt', Sia::sessionPeriode())
                                ->where('jdk.hari', '<>', 0)
                                ->get();

            return view('mahasiswa-member.jdk.jadwal-kuliah', $data);
        }

        public function jadwalKuliahCetak(Request $r)
        {
            $id_reg_pd = Sia::sessionMhs('id_mhs_reg');

            $data['mhs'] = DB::table('mahasiswa_reg as m2')
                        ->leftJoin('mahasiswa as m', 'm.id','=','m2.id_mhs')
                        ->leftJoin('prodi as p', 'm2.id_prodi', '=', 'p.id_prodi')
                        ->leftJoin('semester as smt', 'm2.semester_mulai','=', 'smt.id_smt')
                        ->select('m.id','m.nm_mhs', 'm2.id as id_reg_pd','m2.nim','m.jenkel',
                                'm2.semester_mulai','p.nm_prodi', 'p.jenjang', 'smt.nm_smt', 'smt.id_smt')
                        ->where('m2.id', $id_reg_pd)->first();

            $query = Sia::jadwalKuliahMahasiswa($id_reg_pd, Session::get('jeniskrs_in_jdk'));
            $data['jadwal'] = $query->where('jdk.id_smt', Sia::sessionPeriode())
                                ->where('jdk.hari', '<>', 0)
                                ->get();

            $qr = Sia::sessionMhs('nim').','.Sia::sessionMhs('nama').','.Sia::sessionPeriode('nama');
            QrCode::generate($qr, storage_path().'/qr-code/'.Sia::sessionMhs('nim').'.svg');
            
            return view('mahasiswa-member.jdk.cetak', $data);
        }
    /* End jadwal kuliah */

    public function jadwalUjian(Request $r)
    {
        $jenis_ujian = Sia::jenisUjian(Sia::sessionPeriode());

        $data['jadwal'] = Sia::jadwalUjianMhs(Sia::sessionMhs())->get();

        return view('mahasiswa-member.jdu.index', $data);
    }

    public function jadwalUjianCetak(Request $r)
    {
        $jenis_ujian = Sia::jenisUjian(Sia::sessionPeriode());

        $data['jadwal'] = Sia::jadwalUjianMhs(Sia::sessionMhs())->get();

        $id_reg_pd = Sia::sessionMhs();

        $data['mhs'] = DB::table('mahasiswa_reg as m2')
                        ->leftJoin('mahasiswa as m', 'm.id','=','m2.id_mhs')
                        ->leftJoin('prodi as p', 'm2.id_prodi', '=', 'p.id_prodi')
                        ->leftJoin('semester as smt', 'm2.semester_mulai','=', 'smt.id_smt')
                        ->select('m.id','m.nm_mhs', 'm2.id as id_reg_pd','m2.nim','m.jenkel',
                                'm2.semester_mulai','p.nm_prodi', 'p.jenjang', 'smt.nm_smt', 'smt.id_smt')
                        ->where('m2.id', $id_reg_pd)->first();

        $qr = Sia::sessionMhs('nim').','.Sia::sessionMhs('nama').','.Sia::sessionPeriode('nama');
            QrCode::generate($qr, storage_path().'/qr-code/'.Sia::sessionMhs('nim').'.svg');

        return view('mahasiswa-member.jdu.cetak', $data);
    }

    public function khs(Request $r)
    {
        if ( !empty($r->smt) ) {
            Session::put('smt_in_nilai', $r->smt );
        }


        $data['semester'] = DB::table('semester')
                                ->whereBetween('id_smt', [Sia::sessionMhs('smt_mulai'), Sia::sessionPeriode()])
                                ->orderBy('id_smt','desc')->get();

        $data['kues_aktif'] = DB::table('kues_jadwal')
                                ->where('aktif', 1)
                                ->where('id_prodi', Sia::sessionMhs('prodi'))
                                ->first();

        if ( !empty($r->ubah_jenis) ) {
            Session::put('jeniskrs_in_nilai', $r->ubah_jenis);
        } else {
            if ( !Session::has('jeniskrs_in_nilai') ) {
                Session::put('jeniskrs_in_nilai', 1);
            }
        }

        $data['krs'] = Sia::krsMhs(Sia::sessionMhs('id_mhs_reg'), Session::get('smt_in_nilai'),Session::get('jeniskrs_in_nilai'))
                        ->select('n.*','jdk.kode_kls','jdk.id_jam','mk.id as id_mk','mk.kode_mk','mk.nm_mk','mk.sks_mk')
                        ->get();

        $data['ipk'] = Sia::ipkKhs(Sia::sessionMhs('id_mhs_reg'), Sia::sessionMhs('smt_mulai'), Session::get('smt_in_nilai'));

        return view('mahasiswa-member.khs.khs',$data);
    }

    public function khsCetak(Request $r)
    {
        $data['mhs'] = DB::table('mahasiswa_reg as m2')
                        ->leftJoin('mahasiswa as m', 'm.id','=','m2.id_mhs')
                        ->leftJoin('prodi as p', 'm2.id_prodi', '=', 'p.id_prodi')
                        ->leftJoin('semester as smt', 'm2.semester_mulai','=', 'smt.id_smt')
                        ->leftJoin('konsentrasi as k', 'm2.id_konsentrasi', 'k.id_konsentrasi')
                        ->select('m.nm_mhs','m2.nim', 'm2.semester_mulai','m.jenkel','p.nm_prodi',
                                'p.jenjang', 'smt.nm_smt', 'smt.id_smt', 'k.nm_konsentrasi')
                        ->where('m2.id',Sia::sessionMhs('id_mhs_reg'))->first();
        
        $data['krs'] = Sia::krsMhs(Sia::sessionMhs('id_mhs_reg'), Session::get('smt_in_nilai'), Session::get('jeniskrs_in_nilai'))
                            ->select('n.*','jdk.kode_kls','mk.kode_mk','mk.nm_mk','mk.sks_mk')
                            ->get();

        $data['ipk'] = Sia::ipkKhs(Sia::sessionMhs('id_mhs_reg'), Sia::SessionMhs('smt_mulai'), Session::get('smt_in_nilai'));

        return view('mahasiswa-member.khs.cetak', $data);
    }

    public function transkrip(Request $r)
    {
        $data['krs'] = Sia::transkrip(Sia::sessionMhs());

        return view('mahasiswa-member.transkrip.index',$data);
    }

    public function transkripCetak(Request $r)
    {

        $data['mhs'] = DB::table('mahasiswa_reg as m2')
                        ->leftJoin('mahasiswa as m', 'm.id','=','m2.id_mhs')
                        ->leftJoin('prodi as p', 'm2.id_prodi', '=', 'p.id_prodi')
                        ->leftJoin('konsentrasi as k', 'm2.id_konsentrasi', 'k.id_konsentrasi')
                        ->leftJoin('semester as smt', 'm2.semester_mulai','=', 'smt.id_smt')
                        ->select('m.nm_mhs', 'm.jenkel','m.tempat_lahir','m.tgl_lahir','m2.*','k.nm_konsentrasi','p.nm_prodi', 'p.jenjang', 'smt.nm_smt', 'smt.id_smt')
                        ->where('m2.id', Sia::sessionMhs())->first();

        $data['krs'] = Sia::transkrip(Sia::sessionMhs());

        $qr = Sia::sessionMhs('nim').','.Sia::sessionMhs('nama').','.Sia::sessionPeriode('nama');
        QrCode::generate($qr, storage_path().'/qr-code/'.Sia::sessionMhs('nim').'.svg');

        return view('mahasiswa-member.transkrip.print-transkrip-sementara',$data);
    }

    public function kartuUjian(Request $r)
    {
        $prodi = Sia::sessionMhs('prodi');

        if ( $prodi == '61101' ) {
            $data['jenis'] = Sia::jenisUjianPasca(Sia::sessionPeriode());
        } else {
            $data['jenis'] = Sia::jenisUjian(Sia::sessionPeriode());
        }

        $data['kartu'] = DB::table('kartu_ujian')
                ->where('id_mhs_reg', Sia::sessionMhs())
                ->where('id_smt', Sia::sessionPeriode())
                ->where('jenis', $data['jenis'])
                ->first();

        return view('mahasiswa-member.kartu-ujian.index', $data);
    }

    public function uploadFotoLulus(Request $r)
    {
        $this->validate($r, [
            'foto' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
        ]);

        $whitelist = array('jpg', 'jpeg', 'png', 'gif', 'svg');

        if ( $r->hasFile('foto') ) {
            $extension = $r->foto->getClientOriginalExtension();
            $imageName = Sia::sessionMhs('nim') .'.'.$extension;
            
            if (!in_array(strtolower($extension), $whitelist)) {
                $error = 'Type file salah. Pastikan anda mengupload file '.implode(',', $whitelist);
                return Response::json(['error'  => 1,'msg' => $error]);
            } else {
                $path = config('app.foto-mhs');
                $r->foto->move($path, $imageName);
                // Generate thumbnail
                $img = Image::make($path.'/'.$imageName);
                $img->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($path.'/thumb/'.$imageName);

                $mhs = Mahasiswa::find(Sia::SessionMhs('id_mhs'));
                $mhs->foto_lulus = $imageName;
                $mhs->save();
                Rmt::success('Berhasil mengubah foto');
            }

        }

    }

    public function jurnal(Request $r)
    {
        $data['mhs'] = DB::table('mahasiswa_reg as m2')
                        ->leftJoin('mahasiswa as m', 'm.id','=','m2.id_mhs')
                        ->leftJoin('prodi as p', 'm2.id_prodi', '=', 'p.id_prodi')
                        ->leftJoin('semester as smt', 'm2.semester_mulai','=', 'smt.id_smt')
                        ->select('m.id','m.nm_mhs', 'm2.id as id_reg_pd','m2.nim','m.jenkel',
                                'm2.jurnal_file','m2.jurnal_published','m2.jurnal_approved','m2.pesan_revisi',
                                'm2.semester_mulai','p.nm_prodi', 'p.jenjang', 'smt.nm_smt', 'smt.id_smt')
                        ->where('m2.id', Sia::sessionMhs())->first();

        return view('mahasiswa-member.jurnal', $data);
    }

    public function jurnalStore(Request $r)
    {

        if ( $r->hasFile('file') ) {

            try {

                $ekstArr = ['docx'];
                $ekstensi = $r->file->getClientOriginalExtension();

                if ( !in_array($ekstensi, $ekstArr) ) {
                    return Response::json(['error' => 1, 'msg' => 'Jenis file yang diperbolehkan adalah '.implode(',', $ekstArr)]);
                }

                $fileName = 'jurnal-'.Sia::sessionMhs('nim') .'.'.strtolower($ekstensi);
                $path = config('app.jurnaldir');
                $upload = $r->file->move($path, $fileName);
                $mhs = Mahasiswareg::find(Sia::sessionMhs());
                $mhs->jurnal_file = $fileName;
                $mhs->updated_jurnal = Carbon::now()->format('Y-m-d H:i:s');
                $mhs->save();

                Rmt::success('Berhasil menyimpan jurnal');

            } catch( \Exception $e ) {
                return Response::json(['error' => 1, 'msg' => $e->getMessage()]);
            }

        } else {
            return Response::json(['error' => 1, 'msg' => 'Belum ada file']);
        }
    }

    public function jurnalDownload()
    {
        $mhs = Mahasiswareg::findOrFail(Sia::sessionMhs());
        $path = config('app.jurnaldir');
        $pathToFile = $path.'/'.$mhs->jurnal_file;
        
        if ( file_exists($pathToFile) ) {
            return Response::file($pathToFile);
        } else {
            echo "<center><h4>File tidak ditemukan</h4></center>";
        }
    }

    public function jurnalFileDelete(Request $r)
    {

        try {

            $mhs = Mahasiswareg::findOrFail($r->id_mhs_reg);
            $path = config('app.jurnaldir');
            $file = $path.'/'.$mhs->jurnal_file;
            if ( file_exists($file) ) {
                unlink($file);
            }

            $mhs->jurnal_file = '';
            $mhs->save();

            Rmt::success('Berhasil menghapus jurnal');

        } catch( \Exception $e ) {
            Rmt::error($e->getMessage());
            return redirect()->back();
        }

        return redirect()->back();
    }

    public function pilihKonsentrasi(Request $r)
    {
        $id_mhs_reg = Sia::sessionMhs();

        $mhs = DB::table('mahasiswa_reg as m2')
                ->leftJoin('mahasiswa as m', 'm.id','=','m2.id_mhs')
                ->leftJoin('prodi as p', 'm2.id_prodi', '=', 'p.id_prodi')
                ->select('m.id','m.nm_mhs', 'm2.id as id_reg_pd','m2.nim','m.jenkel','m2.id_prodi',
                        'm2.semester_mulai','p.nm_prodi', 'p.jenjang')
                ->where('m2.id', $id_mhs_reg)->first();

        $data['konsentrasi'] = konsentrasiPps::where('id_mhs_reg', $id_mhs_reg)
                                ->where('id_smt', Sia::sessionPeriode())
                                ->first();

        $data['smt_mhs'] = Sia::posisiSemesterMhs($mhs->semester_mulai, Sia::sessionPeriode());
        $data['mhs'] = $mhs;

        return view('mahasiswa-member.pilih-konsentrasi', $data);
    
    }

    public function konsentrasiStore(Request $r)
    {
        $this->validate($r, [
            'kelas' => 'required',
            'konsentrasi' => 'required'
        ]);

        try {

            DB::transaction(function()use($r) {
                $data = new konsentrasiPps;
                $data->id_smt = Sia::sessionPeriode();
                $data->id_mhs_reg = Sia::sessionMhs();
                $data->id_konsentrasi = $r->konsentrasi;
                $data->kelas = $r->kelas;
                $data->save();

                $mhs = Mahasiswareg::find(Sia::sessionMhs());
                $mhs->id_konsentrasi = $r->konsentrasi;
                $mhs->save();
            });
            
            Rmt::success('Berhasil menyimpan data');

        } catch(\Exception $e) {
            return Response::json([$e->getMessage()], 422);
        }


    }
}
