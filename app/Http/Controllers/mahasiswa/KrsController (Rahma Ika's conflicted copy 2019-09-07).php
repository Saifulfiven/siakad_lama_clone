<?php

namespace App\Http\Controllers\mahasiswa;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Sia, Rmt, DB, Response,Auth,Session,QrCode;

class KrsController extends Controller
{
    public function index(Request $r)
    {

        $mhs = \App\Mahasiswareg::find(Sia::sessionMhs());
        $periode = Sia::sessionPeriode();

        $krs_stat = $this->cekStatusKrs($mhs);
        $posisi_semester = Sia::posisiSemesterMhs($mhs->semester_mulai);
        // $posisi_semester = $this->semesterReal($mhs->id, $posisi_semester);
        $posisi_semester = 5;

        $data['krs_stat'] = $krs_stat;
        $data['mhs'] = $mhs;
        $data['msg'] = '';
        $data['matakuliah'] = [];

        if ( empty($mhs) ) {
            return redirect(route('login'));
        }

        if ( $mhs->jenis_keluar != 0 ) {
            $data['msg'] = 'Anda telah lulus';
        }

        $jad_krs = Sia::cekJadwalKrs(Sia::sessionMhs('prodi'));
        
        if ( !$jad_krs ) {
            $data['msg'] = 'Krs Belum terbuka atau telah tertutup';
            return view('mahasiswa-member.krs.index', $data);
        }

        if ( $krs_stat['status'] == 'lock' ) {

            $krs_tmp = Sia::krsMhsTmp($mhs->id, $periode);
            $krs = Sia::krsMhs($mhs->id,Sia::sessionPeriode(), $krs_stat['jenis'])->get();
            if ( count($krs) == 0 ) {
                $data['krs'] = $krs_tmp;
            } else {
                $data['krs'] = $krs;
            }

        } elseif ( $krs_stat['status'] == 'unlock' ) {

            // jenis jdk = jadwal kuliah
            if ( $krs_stat['jenis'] == 1 ) {

                // Untuk menampilkan KRS Mahasiswa yang tidak bisa krs online
                $data['krs'] = Sia::krsMhs($mhs->id,Sia::sessionPeriode(), $krs_stat['jenis'])->get();
                
                // Jika peserta didik baru
                if ( $mhs->jenis_daftar == 1 ) {

                    if ( $posisi_semester <= 8 ) {

                        $di_krs_tmp = $this->diKrsTmp($mhs->id);
                        $telah_diprogram = $this->telahDiprogram($mhs->id);

                        if ( !empty($mhs->id_konsentrasi) ) {
                            $data['matakuliah'] = $this->getMkPdb($mhs, $posisi_semester, $telah_diprogram, $di_krs_tmp)
                                                ->union($this->mkKonsentrasi($mhs, $posisi_semester, $telah_diprogram, $di_krs_tmp))
                                                ->orderBy('smt')
                                                ->orderBy('jenis_mk', 'asc')
                                                ->groupBy('id_mk')
                                                ->get();
                        } else {
                            $data['matakuliah'] = $this->getMkPdb($mhs, $posisi_semester, $telah_diprogram, $di_krs_tmp)
                                                ->orderBy('smt')
                                                ->get();
                        }
                        $data['mk_error'] = $this->mkError($mhs)->get();

                        $data['sks_diambil'] = $this->sumSksDiambil($mhs->id);
                    
                    } else {
                        
                        $data['mk_error'] = $this->mkError($mhs)->get();

                        $data['sks_diambil'] = $this->sumSksDiambil($mhs->id);
                    }

                }

                // Transfer dll
                else {

                    $data['matakuliah'] = $this->mkTransfer($mhs, $krs_stat['jenis'])
                            ->union($this->mkError($mhs, $krs_stat['jenis']))
                            ->orderBy('smt')->orderBy('nm_mk')
                            ->groupBy('id_mk')
                            ->get();
                }

            } else {
                $data['msg'] = 'Krs semester pendek belum bisa diisi secara online';
            }

            $data['matakuliah_diambil'] = $this->matakuliahDiambil($mhs)->get();
        }

    	return view('mahasiswa-member.krs.index', $data);
    }

    private function cekStatusKrs($mhs)
    {

        $data = DB::table('krs_status')
                    ->where('id_mhs_reg', $mhs->id)
                    ->where('id_smt', Sia::sessionPeriode())
                    ->where('jenis', 'KULIAH')
                    ->first();

        $sp = DB::table('krs_status')
                    ->where('id_mhs_reg', $mhs->id)
                    ->where('id_smt', Sia::sessionPeriode())
                    ->where('jenis', 'SP')
                    ->first();

        if ( empty($sp) ) {
            if ( !empty($data) ) {
                if ( $data->status_krs == 1 ) {
                    $status = 'lock';
                } else {
                    $status = 'unlock';
                }

            } else {

                $status = 'belum-bayar';
            }

            $jenis = 1;

        }
        else 
        {

            if ( !empty($sp) ) {

                if ( $sp->status_krs == 1 ) {
                    $status = 'lock';
                } else {
                    $status = 'unlock';
                }

            } else {

                $status = 'belum-bayar';
            }

            $jenis = 2;
        }

        return ['jenis' => $jenis, 'status' => $status];
    }

    private function sumSksDiambil($id_mhs_reg)
    {
        $data = DB::table('krs_mhs as km')
                ->leftJoin('mk_kurikulum as mkur', 'km.id_mkur', 'mkur.id')
                ->leftJoin('matakuliah as mk', 'mk.id', 'mkur.id_mk')
                    ->where('km.id_smt', Sia::sessionPeriode())
                    ->where('km.id_mhs_reg', $id_mhs_reg)
                    ->sum('mk.sks_mk');
        return $data;
    }

    private function getMkPdb($mhs, $smstr, $telah_diprogram, $di_krs_tmp, $jenis = 1)
    {
        if ( $smstr == 8 ) {
            $smstr = 7;
        }

        // Get mk berdasarkan semester dan kurikulum
        $data = DB::table('kurikulum as kur')
                ->leftJoin('mk_kurikulum as mkur', 'kur.id', 'mkur.id_kurikulum')
                ->leftJoin('matakuliah as mk', 'mk.id', 'mkur.id_mk')
                ->select('mkur.id as id_mkur','mkur.id_mk','mk.jenis_mk','mk.mk_terganti','mk.kode_mk','mk.nm_mk','mk.sks_mk', 'mkur.smt',
                            DB::raw('\'pdb\' as asal'))
                ->where('mkur.smt', $smstr)
                ->where('mkur.id_kurikulum', $mhs->id_kurikulum)
                ->whereNotIn('mkur.id_mk', $di_krs_tmp)
                ->whereNotIn('mkur.id_mk', $telah_diprogram)
                ->where('mk.id_konsentrasi',0)
                ->orderBy('mk.jenis_mk');
        // dd($data->get());
                // dd($mhs->id_konsentrasi);
        return $data;
    }

    private function mkKonsentrasi($mhs, $smstr, $telah_diprogram, $di_krs_tmp)
    {

        $data = DB::table('kurikulum as kur')
                ->leftJoin('mk_kurikulum as mkur', 'kur.id', 'mkur.id_kurikulum')
                ->leftJoin('matakuliah as mk', 'mk.id', 'mkur.id_mk')
                ->select('mkur.id as id_mkur','mkur.id_mk','mk.jenis_mk', 'mk.mk_terganti','mk.kode_mk','mk.nm_mk','mk.sks_mk', 'mkur.smt',
                            DB::raw('\'pdb\' as asal'))
                ->where('mkur.smt', $smstr)
                ->where('mkur.id_kurikulum', $mhs->id_kurikulum)
                ->whereNotIn('mkur.id_mk', $di_krs_tmp)
                ->whereNotIn('mkur.id_mk', $telah_diprogram)
                ->where('mk.id_konsentrasi', $mhs->id_konsentrasi);
        return $data;
    }

    private function mkError($mhs, $jenis = 1)
    {
        $pengganti = $this->mkPengganti($mhs->id);
        $telah_diprogram = $this->telahDiprogram($mhs->id);

        // BUG: Jika ada dua matakuliah yg sama (beda kurikulum) pada jadwal
        // Maka fungsi ini akan menampilkan keduanya
        $mk_error = DB::table('nilai as n')
                ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
                ->leftJoin('mk_kurikulum as mkur', 'mkur.id', 'jdk.id_mkur')
                ->leftJoin('matakuliah as mk', 'mkur.id_mk','mk.id')
                ->where('n.id_mhs_reg', $mhs->id)
                ->where('n.nilai_indeks', '<=', 1)
                ->where('n.nilai_huruf','<>','')
                ->whereNotNull('n.nilai_huruf')
                ->where(function($q){
                    $q->where('mk.kelompok_mk', 'E')
                        ->orWhere('mkur.periode', Sia::sessionPeriode('smt'));
                })
                ->whereNotIn('jdk.id_mk', $pengganti)
                ->whereNotIn('jdk.id_mk', $telah_diprogram)
                ->pluck('mkur.id_mk');
                // ->pluck('mk.nm_mk');
                // dd($mk_error);


        $di_krs_tmp = $this->diKrsTmp($mhs->id);

        $data = DB::table('kurikulum as kur')
                ->leftJoin('mk_kurikulum as mkur', 'kur.id','mkur.id_kurikulum')
                ->leftJoin('matakuliah as mk', function($join){
                    $join->on('mkur.id_mk','=','mk.id')
                        ->orOn('mkur.id_mk', '=', 'mk.mk_terganti');
                })
                ->select('mkur.id as id_mkur', 'mk.id as id_mk','mk.jenis_mk','mk.mk_terganti','mk.kode_mk','mk.nm_mk','mk.sks_mk', 'mkur.smt',
                        DB::raw('\'error\' as asal'))
                ->where(function($q)use($mk_error){
                    $q->whereIn('mk.id', $mk_error)
                        ->orWhereIn('mk.mk_terganti', $mk_error);
                })
                ->whereNotIn('mkur.id_mk', $di_krs_tmp);

        return $data;
    }

    private function diKrsTmp($id_mhs_reg)
    {
        $krs = DB::table('krs_mhs as km')
                ->leftJoin('mk_kurikulum as mkur', 'km.id_mkur', 'mkur.id')
                    ->where('km.id_smt', Sia::sessionPeriode())
                    ->where('km.id_mhs_reg', $id_mhs_reg)
                    ->pluck('mkur.id_mk');
        return $krs;
    }

    private function telahDiprogram($id_mhs_reg)
    {
        $data = DB::table('nilai as n')
                ->leftJoin('jadwal_kuliah as jdk', 'jdk.id', 'n.id_jdk')
                ->leftJoin('matakuliah as mk', 'jdk.id_mk', 'mk.id')
                ->where('n.id_mhs_reg', $id_mhs_reg)
                ->whereIn('n.nilai_huruf', ['A','B','C'])
                ->pluck('jdk.id_mk');

        return $data;
    }

    private function mkPengganti($id_mhs_reg, $jenis = 1)
    {
        // Mengambil mk error yang ada pada kurikulum lain
        $data = DB::table('nilai as n')
                ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
                ->leftJoin('matakuliah as mk', 'mk.id', 'jdk.id_mk')
                ->where('n.id_mhs_reg', $id_mhs_reg)
                ->whereIn('n.nilai_huruf', ['A','B','C'])
                ->where('mk.mk_terganti','<>', '')
                ->pluck('mk.id');

        return $data;
    }

    /* Return semester yang belum diprogramkan
     * return integer
        private function smtBelumDiprogram($mhs)
        {
            $posisi_periode = Sia::sessionPeriode('smt');

            $data = DB::table('mk_kurikulum as mkur')
                    ->leftJoin('matakuliah as mk', 'mkur.id_mk', 'mk.id')
                    ->whereNotIn('mkur.id_mk', 
                        DB::table('nilai as n')
                        ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
                        ->leftJoin('mk_kurikulum as mkur2', 'jdk.id_mkur', 'mkur2.id')
                        ->where('n.id_mhs_reg', $mhs->id)
                        ->whereIn('mkur2.smt', Sia::listJenisSmt($posisi_periode))
                        ->pluck('mkur2.id_mk'))
                    ->where('mkur.id_kurikulum', $mhs->id_kurikulum)
                    ->where('mkur.periode', $posisi_periode)
                    ->where('mk.jenis_mk', 'A')
                    ->whereNull('mk.id_konsentrasi')
                    ->min('mkur.smt');

            return $data ? $data : 1;
        }
    */

    private function tinjauAkm($mhs,$smstr_now)
    {
        // Get AKM mhs selain yang aktif yang berada pada semester terkecil
        $sql_na = DB::select("
            SELECT min(id_smt) as id_smt, status_mhs from (
                SELECT id_smt,status_mhs from aktivitas_kuliah where id_mhs_reg='$mhs->id'
                order by id_smt asc limit 7
            ) as res where status_mhs <> 'A' limit 1"
        );


        if ( count($sql_na) > 0 ) {
            $non_aktif = $sql_na[0]->id_smt;

            $smstr_non_aktif = Sia::posisiSemesterMhs($mhs->semester_mulai, $non_aktif);
            $jns_smt_non_aktif = substr($non_aktif,4,1);

            // Jika Non aktif / cuti di ganjil
            if ( $jns_smt_non_aktif == 1 ) {

                $smt_mk = $smstr_now - $smstr_non_aktif + 1;
                if ( $smt_mk < $smstr_non_aktif ) {
                    $smt_mk = $smstr_now;
                }

            } else {
                // Jika non aktif / cuti di genap;
                $smt_mk = $smstr_now - $smstr_non_aktif;
                if ( $smt_mk < $smstr_non_aktif ) {
                    $smt_mk = $smstr_now;
                }
            }

            return $smt_mk;

        } else {

            return $smstr_now;
        }
    }

    private function semesterReal($id_mhs_reg, $posisi_semester)
    {
        $semester_str = $posisi_semester % 2 == 0 ? 'GENAP' : 'GANJIL';
        $semester_lalu = [];
        $posisi = $posisi_semester;

        for( $i = 1; $i < $posisi_semester ; $i++ ) {
            if ( $semester_str == 'GENAP') {
                if ( $i % 2 != 0 ) continue;
                $semester_lalu[] = $i; 
            } else {
                if ( $i & 2 == 0 ) continue;
                $semester_lalu[] = $i;
            }
        }

        if ( count($semester_str) > 0 ) {
            
            foreach( $semester_lalu as $val ) {
                $total_nilai = DB::table('nilai')
                        ->where('id_mhs_reg', $id_mhs_reg)
                        ->where('semester_mk', $val)
                        ->sum('nilai_indeks');

                if ( empty($total_nilai) ) {
                    $posisi = $val;
                    break;
                }
            }

        }

        // Posisi semester yg belum diprogram
        return $posisi;

    }

    private function mkTransfer($mhs, $jenis = 1)
    {
        $data = DB::table('jadwal_kuliah as jdk')
                ->leftJoin('mk_kurikulum as mkur', 'jdk.id_mkur', 'mkur.id')
                ->leftJoin('kurikulum as k', 'k.id', 'mkur.id_kurikulum')
                ->leftJoin('matakuliah as mk', 'mk.id', 'mkur.id_mk')
                ->select('mkur.id as id_mkur','mkur.id_mk','mk.mk_terganti','mk.kode_mk','mk.nm_mk','mk.sks_mk', 'mkur.smt',DB::raw('\'transfer\' as asal'))
                // ->where('mkur.id_kurikulum', $mhs->id_kurikulum)
                ->whereIn('mkur.id_kurikulum', 
                    DB::table('kurikulum')
                        ->where('id_prodi', Sia::sessionMhs('prodi'))
                        ->pluck('id'))
                // ->where('mkur.periode', Sia::sessionPeriode('smt'))
                ->where('jdk.jenis', $jenis)
                ->where('jdk.id_smt', Sia::sessionPeriode())
                ->whereNotIn('mkur.id_mk',
                        DB::table('nilai as n')
                        ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
                        ->leftJoin('mk_kurikulum as mkur2', 'jdk.id_mkur', 'mkur2.id')
                        ->where('n.id_mhs_reg', $mhs->id)
                        ->whereIn('mkur2.smt', Sia::listJenisSmt(Sia::sessionPeriode('smt')))
                        ->pluck('mkur2.id_mk'))
                ->whereNotIn('mkur.id_mk', $this->diKrsTmp($mhs->id))
                ->whereNotIn('mkur.id_mk', 
                        DB::table('nilai_transfer as nt')
                                ->where('nt.id_mhs_reg', $mhs->id)
                                ->pluck('nt.id_mk'));

        return $data;
    }

    private function matakuliahDiambil($mhs)
    {
        $data = DB::table('krs_mhs as km')
                ->leftJoin('mk_kurikulum as mkur', 'km.id_mkur', 'mkur.id')
                ->leftJoin('matakuliah as mk', 'mk.id', 'mkur.id_mk')
                ->select('km.id','mk.nm_mk','mk.sks_mk', 'mk.jenis_mk','mkur.smt')
                ->where('km.id_smt', Sia::sessionPeriode())
                ->where('km.id_mhs_reg', $mhs->id)
                ->orderBy('mkur.smt')
                ->orderBy('mk.jenis_mk')
                ->orderBy('mk.kode_mk');
        return $data;
    }

    public function storeTmp(Request $r)
	{
        try {
            DB::transaction(function() use($r){
                // $mhs = \App\Mahasiswareg::find(Sia::sessionMhs());
                $id_smt = Sia::sessionPeriode();

                $krs_diambil = DB::table('krs_mhs as km')
                            ->leftJoin('mk_kurikulum as mkur','km.id_mkur', 'mkur.id')
                            ->leftJoin('matakuliah as mk', 'mk.id', 'mkur.id_mk')
                            ->where('km.id_mhs_reg', $r->id_mhs_reg)
                            ->where('km.id_smt', $id_smt)
                            ->sum('mk.sks_mk');

                if ( $r->sks + $krs_diambil > 24 ) {
                    return Response::json(['error' => 1, 'msg' => 'Jumlah SKS yang diprogram tidak boleh melebihi 24 SKS']);
                }

                $data = [
                    'id_mhs_reg' => $r->id_mhs_reg,
                    'id_mkur' => $r->id_mkur,
                    'id_smt' => $id_smt,
                ];
                DB::table('krs_mhs')->insert($data);

			});
		} catch(\Exception $e) {
			return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
		}

		Rmt::success('Berhasil mengambil matakuliah');
	}

    private function simpanTmp($id_mhs_reg, $sks)
    {
        $tmp = DB::table('krs_mhs as km')
                ->leftJoin('mk_kurikulum as mkur', 'km.id_mkur', 'mkur.id')
                ->leftJoin('matakuliah as mk', 'mk.id', 'mkur.id_mk')
                ->select(DB::raw('SUM(mk.sks_mk) as total_sks'))
                ->where('km.id_smt', Sia::sessionPeriode())
                ->where('km.id_mhs_reg', $id_mhs_reg)
                ->first();

        if ( $tmp->total_sks + $sks > 24 ) {

            return false;

        } else {

            $data = [
                'id' => Rmt::uuid(),
                'id_mhs_reg' => $id_mhs_reg,
                'id_smt' => Sia::sessionPeriode(),
                'id_jdk' => $id_jdk,
            ];
            DB::table('krs_tmp')->insert($data);

            return true;
        }
    }

    public function storeTmpArr(Request $r)
    {
        try {
            $id_smt = Sia::sessionPeriode();
            $id_mhs_reg = Sia::sessionMhs();
            $data_krs = json_decode($r->matakuliah);

            foreach( $data_krs as $val ) {

                $sks_diambil = $this->sumSksDiambil($id_mhs_reg);

                if ( $val->sks + $sks_diambil > 24 ) {
                    Rmt::error('Matakuliah tidak tersimpan semua. Jumlah SKS yang diprogram tidak boleh melebihi 24 SKS');
                    break;
                    return Response::json(['error' => 1, 'msg' => $r->sks .'-'.$sks_diambil.'Jumlah SKS yang diprogram tidak boleh melebihi 24 SKS']);
                }

                $data = [
                    'id_mhs_reg' => $id_mhs_reg,
                    'id_mkur' => $val->id_mkur,
                    'id_smt' => $id_smt,
                ];

                DB::table('krs_mhs')->insert($data);
            }
        } catch(\Exception $e) {
            Rmt::error('Terjadi kesalahan. '.$e->getMessage());
            return redirect()->back();
        }

        Rmt::success('Berhasil menyimpan matakuliah');
        return redirect()->back();
    }

    public function deleteTmp($id)
    {
        DB::table('krs_mhs')->where('id', $id)->delete();
        Rmt::success('Berhasil menghapus matakuliah');
        return redirect()->back();
    }

	public function store(Request $r)
	{
        try {

            $id_mhs_reg = Sia::sessionMhs();
            $id_smt = Sia::sessionPeriode();

            $sks_diambil = $this->sumSksDiambil($id_mhs_reg);

            if ( $sks_diambil > 24 ) {
                Rmt::error('Jumlah SKS yang diprogram tidak boleh melebihi 24 SKS. Silahkan hapus beberapa matakuliah');
                return Response::json(['error' => 1, 'msg' => 'Jumlah SKS yang diprogram tidak boleh melebihi 24 SKS']);
            }

            DB::table('krs_status')
                ->where('id_mhs_reg', $id_mhs_reg)
                ->where('id_smt', $id_smt)
                ->where('jenis', 'KULIAH')
                ->update(['status_krs' => '1', 'valid' => '1', 'jalur' => 'online']);
            Rmt::success('Berhasil menyimpan KRS');

        } catch(\Exception $e) {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()]);
        }
	}

    public function cetakKrs(Request $r)
    {
        $periode = Sia::sessionPeriode();

        $data['mhs'] = \App\Mahasiswareg::find(Sia::sessionMhs());
        $krs_tmp = Sia::krsMhsTmp($data['mhs']->id, $periode);
        $krs = Sia::krsMhs($data['mhs']->id, $periode, $r->jenis)->get();
        if ( count($krs) == 0 ) {
            $data['krs'] = $krs_tmp;
        } else {
            $data['krs'] = $krs;
        }

        $qr = 'KRS-'.Sia::sessionMhs('nim').','.Sia::sessionMhs('nama').','.Sia::sessionPeriode('nama');
            
        QrCode::generate($qr, storage_path().'/qr-code/'.Sia::sessionMhs('nim').'.svg');

        return view('mahasiswa-member.krs.cetak-krs', $data);
    }

    public function cetakKsm(Request $r)
    {
        $periode = Sia::sessionPeriode();

        $data['mhs'] = \App\Mahasiswareg::find(Sia::sessionMhs());
        $krs_tmp = Sia::krsMhsTmp($data['mhs']->id, $periode);
        $krs = Sia::krsMhs($data['mhs']->id, $periode, $r->jenis)->get();
        if ( count($krs) == 0 ) {
            $data['krs'] = $krs_tmp;
        } else {
            $data['krs'] = $krs;
        }
        $qr = 'KSM-'.Sia::sessionMhs('nim').','.Sia::sessionMhs('nama').','.Sia::sessionPeriode('nama');
            
        QrCode::generate($qr, storage_path().'/qr-code/'.Sia::sessionMhs('nim').'.svg');

        return view('mahasiswa-member.krs.cetak-ksm', $data);
    }

    private function tes()
    {
        $jadwal = DB::table('jadwal_kuliah as jdk')
                ->leftJoin('mk_kurikulum as mkur', 'jdk.id_mkur', 'mkur.id')
                ->leftJoin('jam_kuliah as jk', 'jdk.id_jam', 'jk.id')
                ->select('jdk.*', 'mkur.id_mk',
                    DB::raw('(SELECT COUNT(*) as agr from '.Sia::prefix().'nilai where id_jdk='.Sia::prefix().'jdk.id) as terisi'),
                    DB::raw('(SELECT COUNT(*) as agr from '.Sia::prefix().'krs_tmp where id_jdk='.Sia::prefix().'jdk.id) as akan_diisi'))
                ->where('mkur.id_mk', 'a79cb4a1-cc49-4335-b4b3-bd6046336da4')
                ->orderBy('jk.jam_masuk')->get();
        // echo count($jadwal);
        // foreach( $jadwal as $r ){
        //     echo $r->id.'<br>';
        // }

        // exit;
    }
}
