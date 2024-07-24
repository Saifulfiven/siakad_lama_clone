<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Rmt, DB, Response;

class KrsController extends Controller
{
    use library;

    public function index(Request $r)
    {
        $mhs = DB::table('mahasiswa_reg as m1')
                ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                ->leftJoin('prodi as pr', 'm1.id_prodi', 'pr.id_prodi')
                ->leftJoin('konsentrasi as k', 'm1.id_konsentrasi', 'k.id_konsentrasi')
                ->leftJoin('dosen as d', 'd.id', 'm1.dosen_pa')
                ->select('m1.*', 'm2.nm_mhs', 'pr.nm_prodi', 'pr.jenjang',
                        'k.nm_konsentrasi','d.gelar_depan','d.nm_dosen','d.gelar_belakang')
                ->where('m1.nim', $r->nim)
                ->orderBy('m1.semester_mulai', 'desc')
                ->first();

        if( empty($mhs) ) { 
            return Response::json(['error' =>1 , 'msg' => 'Tidak ada data bisa ditampilkan']);
        }

        $jadwal_diambil = [];

        // Get Periode Berjalan
        if ( $mhs->id_prodi == 61101 ) {
            $periode = $this->semesterBerjalanS2($mhs->id);
            if ( !empty($periode) ) {
                $periode_id = $periode->id_smt;
                $periode_nama = $periode->nm_smt;
                $periode_alias = $periode->smt;
            } else {
                $periode = $this->semesterBerjalan($mhs->id);
                $periode_id = $periode['id'];
                $periode_nama = $periode['nama'];
                $periode_alias = $periode['ket'];
            }
        } else {
            $periode = $this->semesterBerjalan($mhs->id);
            $periode_id = $periode['id'];
            $periode_nama = $periode['nama'];
            $periode_alias = $periode['ket'];
        }

        $krs_stat = $this->cekStatusKrs($mhs, $periode_id);

        $matakuliah = [];

        if ( $krs_stat['status'] == 'lock' ) {

            $krs = $this->krsMhs($mhs->id, $periode_id, $krs_stat['jenis'])->get();
        
        } elseif ( $krs_stat['status'] == 'unlock' ) {

            // jenis jdk = jadwal kuliah
            if ( $krs_stat['jenis'] == 1 ) {

                // Jika peserta didik baru
                if ( $mhs->jenis_daftar == 1 ) {

                    $smt_belum_diprogram = $this->smtBelumDiprogram($mhs, $periode_alias);

                    $matakuliah = $this->getMkPdb($mhs,$smt_belum_diprogram, $periode_id)
                                            ->union($this->mkError($mhs, $periode_id))
                                            ->orderBy('smt')->orderBy('kode_mk')
                                            ->groupBy('id_mk')
                                            ->get();
                }

                // Transfer dll
                else {

                    $matakuliah = $this->mkTransfer($mhs, $krs_stat['jenis'])
                            ->union($this->mkError($mhs, $periode_id, $krs_stat['jenis']))
                            ->orderBy('smt')->orderBy('nm_mk')
                            ->groupBy('id_mk')
                            ->get();
                }

            }

            // Jenis jdk = semester pendek (2)
            else {

                $matakuliah = $this->mkError($mhs, $periode_id, $krs_stat['jenis'])
                                ->orderBy('mkur.smt')->orderBy('mk.kode_mk')
                                ->groupBy('mk.id_mk')
                                ->get();
            }

            $matakuliah_diambil = $this->matakuliahDiambil($mhs, $periode_id)->get();
        }

        $krs = $this->krsMhs($mhs->id, $periode_id, $krs_stat['jenis'])->get();

        $mahasiswa = [
            'nim' => $mhs->nim,
            'nama' => $mhs->nm_mhs,
            'prodi' => $mhs->nm_prodi.' ('.$mhs->jenjang.')',
            'konsentrasi' => $mhs->nm_konsentrasi,
            'smstr' => $this->posisiSemesterMhs($mhs->semester_mulai, $periode_id),
            'dosen' => $mhs->gelar_depan.' '.$mhs->nm_dosen.' '.$mhs->gelar_belakang,
            'smt' => $periode_nama,
            'angkatan' => substr($mhs->nim, 0, 4),
            'jenis_daftar' => $mhs->jenis_daftar
        ];

        $no = 1;
        foreach( $matakuliah_diambil as $md )
        {
            $jadwal_diambil[] = [
                'no' => $no++,
                'nm_mk' => $md->nm_mk,
                'sks_mk' => $md->sks_mk,
                'kode_kls' => $md->kode_kls,
                'jam_masuk' => substr($md->jam_masuk, 0, 5),
                'smstr' => $md->smt
            ];
        }

        $data = ['jadwal' => $matakuliah, 'jadwal_diambil' => $jadwal_diambil, 'krs' => $krs, 'mhs' => $mahasiswa, 'status' => $krs_stat['status'] ];

    	$result = ['error' => 0, 'data' => $data];
        
        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }

    private function cekStatusKrs($mhs, $id_smt)
    {

        $data = DB::table('krs_status')
                    ->where('id_mhs_reg', $mhs->id)
                    ->where('id_smt', $id_smt)
                    ->where('jenis', 'KULIAH')
                    ->first();

        $sp = DB::table('krs_status')
                    ->where('id_mhs_reg', $mhs->id)
                    ->where('id_smt', $id_smt)
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

    private function diKrsTmp($id_mhs_reg, $smt)
    {
        $krs = DB::table('krs_tmp as kt')
                ->leftJoin('jadwal_kuliah as jdk2', 'kt.id_jdk', 'jdk2.id')
                ->leftJoin('mk_kurikulum as mkur2', 'jdk2.id_mkur', 'mkur2.id')
                    ->where('kt.id_smt', $smt)
                    ->where('kt.id_mhs_reg', $id_mhs_reg)
                    ->pluck('mkur2.id_mk');
        return $krs;
    }

    private function getMkPdb($mhs, $smstr, $smt, $jenis = 1)
    {
        // Get mk berdasarkan semester dan kurikulum
        $data = DB::table('jadwal_kuliah as jdk')
                ->leftJoin('mk_kurikulum as mkur', 'jdk.id_mkur', 'mkur.id')
                ->leftJoin('matakuliah as mk', 'mk.id', 'mkur.id_mk')
                ->leftJoin('jam_kuliah as jk', 'jdk.id_jam', 'jk.id')
                ->select('mkur.id_mk','mk.mk_terganti','mk.kode_mk','mk.nm_mk','mk.sks_mk', 'mkur.smt',
                            DB::raw('\'pdb\' as asal'))
                ->where('jdk.id_smt', $smt)
                ->where('jdk.id_prodi', $mhs->id_prodi)
                ->where('jdk.jenis', $jenis)
                ->where('mkur.smt', $smstr)
                ->where('mkur.id_kurikulum', $mhs->id_kurikulum)
                // ->where('jk.ket', $mhs->jam_kuliah)
                ->whereNotIn('mkur.id_mk', $this->diKrsTmp($mhs->id, $smt));
        
        return $data;
    }

    private function mkError($mhs, $smt, $jenis = 1)
    {
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
                ->pluck('mkur.id_mk');
                // dd($mk_error);exit;
                
        if ( $jenis == 1 ) {
            $mk_error->where('mkur.periode', substr($smt, 4, 1));
        }

        $di_krs_tmp = $this->diKrsTmp($mhs->id, $smt);

        $data = DB::table('jadwal_kuliah as jdk')
                ->leftJoin('mk_kurikulum as mkur', 'jdk.id_mkur','mkur.id')
                ->leftJoin('matakuliah as mk', function($join){
                    $join->on('mkur.id_mk','=','mk.id')
                        ->orOn('mkur.id_mk', '=', 'mk.mk_terganti');
                })
                ->select('mk.id as id_mk','mk.mk_terganti','mk.kode_mk','mk.nm_mk','mk.sks_mk', 'mkur.smt',
                        DB::raw('\'error\' as asal'))
                ->where(function($q)use($mk_error){
                    $q->whereIn('mk.id', $mk_error)
                        ->orWhereIn('mk.mk_terganti', $mk_error);
                })
                ->where('jdk.id_smt', $smt)
                ->where('jdk.jenis', $jenis)
                ->whereNotIn('mkur.id_mk', $di_krs_tmp);

        return $data;
    }

    private function mkPengganti($mhs, $smt, $jenis = 1)
    {
        // Mengambil mk error yang ada pada kurikulum lain
        $data = DB::table('nilai as n')
                ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
                ->leftJoin('mk_kurikulum as mkur', 'mkur.id', 'jdk.id_mkur')
                ->leftJoin('kurikulum as k', 'k.id', 'mkur.id_kurikulum')
                ->leftJoin('matakuliah as mp', 'mp.mk_terganti', 'mkur.id_mk')
                ->leftJoin('matakuliah as mk', 'mk.id', 'mp.mk_terganti')
                ->select('mkur.id_mk','mk.mk_terganti','mk.kode_mk','mk.nm_mk','mk.sks_mk', 'mkur.smt',
                        DB::raw('\'pengganti\' as asal'))
                ->where('n.id_mhs_reg', $mhs->id)
                ->where('n.nilai_indeks', 0)
                ->where('mkur.periode', substr($smt, 4, 1))
                ->where('jdk.id_smt', $smt)
                ->where('jdk.jenis', $jenis)
                ->whereNotIn('mkur.id_mk', $this->diKrsTmp($mhs->id, $smt));

        return $data;
    }

    /* Return semester yang belum diprogramkan
     * return integer
    */
    private function smtBelumDiprogram($mhs, $periode_alias)
    {

        $data = DB::table('mk_kurikulum as mkur')
                ->leftJoin('matakuliah as mk', 'mkur.id_mk', 'mk.id')
                ->whereNotIn('mkur.id_mk', 
                    DB::table('nilai as n')
                    ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
                    ->leftJoin('mk_kurikulum as mkur2', 'jdk.id_mkur', 'mkur2.id')
                    ->where('n.id_mhs_reg', $mhs->id)
                    ->whereIn('mkur2.smt', $this->listJenisSmt($periode_alias))
                    ->pluck('mkur2.id_mk'))
                ->where('mkur.id_kurikulum', $mhs->id_kurikulum)
                ->where('mkur.periode', $periode_alias)
                ->where('mk.jenis_mk', 'A') /* Jenis mk wajib */
                ->whereNull('mk.id_konsentrasi')
                ->min('mkur.smt');

        return $data ? $data : 1;
    }

    private function mkTransfer($mhs, $smt, $jenis = 1)
    {
        $data = DB::table('jadwal_kuliah as jdk')
                ->leftJoin('mk_kurikulum as mkur', 'jdk.id_mkur', 'mkur.id')
                ->leftJoin('kurikulum as k', 'k.id', 'mkur.id_kurikulum')
                ->leftJoin('matakuliah as mk', 'mk.id', 'mkur.id_mk')
                ->select('mkur.id_mk','mk.mk_terganti','mk.kode_mk','mk.nm_mk','mk.sks_mk', 'mkur.smt',DB::raw('\'transfer\' as asal'))
                // ->where('mkur.id_kurikulum', $mhs->id_kurikulum)
                ->whereIn('mkur.id_kurikulum', 
                    DB::table('kurikulum')
                        ->where('id_prodi', $mhs->id_prodi)
                        ->pluck('id'))
                // ->where('mkur.periode', Sia::sessionPeriode('smt'))
                ->where('jdk.jenis', $jenis)
                ->where('jdk.id_smt', $smt)
                ->whereNotIn('mkur.id_mk',
                        DB::table('nilai as n')
                        ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
                        ->leftJoin('mk_kurikulum as mkur2', 'jdk.id_mkur', 'mkur2.id')
                        ->where('n.id_mhs_reg', $mhs->id)
                        ->whereIn('mkur2.smt', $this->listJenisSmt(substr($smt, 4, 1)))
                        ->pluck('mkur2.id_mk'))
                ->whereNotIn('mkur.id_mk', $this->diKrsTmp($mhs->id, $smt))
                ->whereNotIn('mkur.id_mk', 
                        DB::table('nilai_transfer as nt')
                                ->where('nt.id_mhs_reg', $mhs->id)
                                ->pluck('nt.id_mk'));

        return $data;
    }

    private function matakuliahDiambil($mhs, $smt)
    {
        $data = DB::table('krs_tmp as kt')
                ->leftJoin('jadwal_kuliah as jdk', 'jdk.id', 'kt.id_jdk')
                ->leftJoin('mk_kurikulum as mkur', 'jdk.id_mkur', 'mkur.id')
                ->leftJoin('matakuliah as mk', 'mk.id', 'mkur.id_mk')
                ->leftJoin('jam_kuliah as jam', 'jdk.id_jam', 'jam.id')
                ->select('kt.id','mk.nm_mk','mk.sks_mk', 'mkur.smt',
                        'jam.jam_masuk', 'jdk.kode_kls', 'jdk.hari')
                ->where('kt.id_smt', $smt)
                ->where('kt.id_mhs_reg', $mhs->id)
                ->orderBy('mkur.smt')->orderBy('mk.kode_mk');
        return $data;
    }

    private function krsMhs($id_mhs_reg,$id_smt = '', $jenis = 1)
    {
        // Jenis 1 = KULIAH, 2 = SP
        $smt = $id_smt;

        $data = DB::table('nilai as n')
            ->leftJoin('jadwal_kuliah as jdk', 'jdk.id', '=', 'n.id_jdk')
            ->leftJoin('mk_kurikulum as mkur', 'jdk.id_mkur', 'mkur.id')
            ->leftJoin('matakuliah as mk', 'mk.id', '=', 'mkur.id_mk')
            ->select('n.*','jdk.kode_kls','mk.kode_mk','mk.nm_mk','mk.sks_mk','jdk.id_smt')
            ->where('n.id_mhs_reg', $id_mhs_reg)
            ->where('jdk.jenis',$jenis)
            ->where('jdk.id_smt', $smt);

        return $data;
    }

    public function storeTmp(Request $r)
	{
        try {
            DB::transaction(function() use($r){
                $mhs = \App\Mahasiswareg::find($r->id_mhs_reg);

                $jadwal = DB::table('jadwal_kuliah as jdk')
                            ->leftJoin('mk_kurikulum as mkur', 'jdk.id_mkur', 'mkur.id')
                            ->leftJoin('jam_kuliah as jk', 'jdk.id_jam', 'jk.id')
                            ->select('jdk.*', 'mkur.id_mk',
                                DB::raw('(SELECT COUNT(*) as agr from nilai where id_jdk=jdk.id) as terisi'),
                                DB::raw('(SELECT COUNT(*) as agr from krs_tmp where id_jdk=jdk.id) as akan_diisi'))
                            ->where('mkur.id_mk', $r->id_mk)
                            ->where('jk.ket', $mhs->jam_kuliah)
                            ->orderBy('jk.jam_masuk')->get();

                $count_jdk = count($jadwal);
                
                if ( $count_jdk > 0 ) {

                    $no = 1;

                    foreach( $jadwal as $jdk ) {

                        // Kapasitas kelas cukup
                            if ( ($jdk->terisi + $jdk->akan_diisi) <= $jdk->kapasitas_kls ) {
                                
                                // Cek bentrok jam
                                $bentrok = DB::table('krs_tmp as krs')
                                            ->leftJoin('jadwal_kuliah as jdk', 'jdk.id', 'krs.id_jdk')
                                            ->where('jdk.hari', $jdk->hari)
                                            ->where('jdk.id_jam', $jdk->id_jam)
                                            ->where('krs.id_mhs_reg', $r->id_mhs_reg)
                                            ->where('krs.id_smt', $r->id_smt)
                                            ->count();

                                if ( $bentrok > 0 ) {

                                    if ( $no++ == $count_jdk ) {

                                        $this->storeTmp2($r, $mhs);
                                        break;

                                    } else {
                                        continue;
                                    }

                                } else {

                                    // Jika jadwal khusus (muslim/non muslim)
                                    if ( !empty($jdk->kelas_khusus) ) {

                                        if ( $jdk->kelas_khusus == 1 && $mhs->mahasiswa->id_agama == 1 ) {
                                            if ( !$this->simpanTmp($jdk->id, $r->id_mhs_reg, $r->sks) ) {
                                                return Response::json(['error' => 1, 'msg' => 'Maksimal SKS yang dapat diprogram telah melebihi batas maksimal']);
                                            }
                                        } elseif ( $jdk->kelas_khusus == 2 && $mhs->mahasiswa->id_agama != 1 ) {
                                            if ( !$this->simpanTmp($jdk->id, $r->id_mhs_reg, $r->sks) ) {
                                                return Response::json(['error' => 1, 'msg' => 'Maksimal SKS yang dapat diprogram telah melebihi batas maksimal']);
                                            }
                                        } else {
                                            continue;
                                        }

                                    } else {

                                        if ( !$this->simpanTmp($jdk->id, $r->id_mhs_reg, $r->sks) ) {
                                            return Response::json(['error' => 1, 'msg' => 'Maksimal SKS yang dapat diprogram telah melebihi batas maksimal']);
                                        }
                                    }

                                }

                                break;

                        // Kapasitas kelas tidak cukup
                            } else {

                                if ( $no++ == $count_jdk ) {
                                    
                                    if ( !$this->simpanTmp($jdk->id, $r->id_mhs_reg, $r->sks) ) {
                                        return Response::json(['error' => 1, 'msg' => 'Maksimal SKS yang dapat diprogram telah melebihi batas maksimal']);
                                    }
                                    break;

                                } else {
                                    continue;
                                }
                            }
                    }
                
                } else {
                    return Response::json(['error' => 1, 'msg' => 'Terjadi kesalahan, muat ulang halaman dan ulangi lagi. Apabila masih belum bisa segera hubungi bagian akademik'], 200);
                }

			});
		} catch(\Exception $e) {
			return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
		}
                
		Rmt::success('Berhasil mengambil matakuliah');
	}

    private function simpanTmp($smt, $id_jdk, $id_mhs_reg, $sks)
    {
        $tmp = DB::table('krs_tmp as kt')
                ->leftJoin('jadwal_kuliah as jdk', 'jdk.id', 'kt.id_jdk')
                ->leftJoin('mk_kurikulum as mkur', 'jdk.id_mkur', 'mkur.id')
                ->leftJoin('matakuliah as mk', 'mk.id', 'mkur.id_mk')
                ->select(DB::raw('SUM(mk.sks_mk) as total_sks'))
                ->where('kt.id_smt', $smt)
                ->where('kt.id_mhs_reg', $id_mhs_reg)
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

    private function storeTmp2($r, $mhs)
    {
        $jadwal = DB::table('jadwal_kuliah as jdk')
                    ->leftJoin('mk_kurikulum as mkur', 'jdk.id_mkur', 'mkur.id')
                    ->leftJoin('jam_kuliah as jk', 'jdk.id_jam', 'jk.id')
                    ->select('jdk.*', 'mkur.id_mk',
                        DB::raw('(SELECT COUNT(*) as agr from '.Sia::prefix().'nilai where id_jdk='.Sia::prefix().'jdk.id) as terisi'),
                        DB::raw('(SELECT COUNT(*) as agr from '.Sia::prefix().'krs_tmp where id_jdk='.Sia::prefix().'jdk.id) as akan_diisi'))
                    ->where('mkur.id_mk', $r->id_mk)
                    ->where('jk.ket', '<>', $mhs->jam_kuliah)
                    ->orderBy('jk.jam_masuk')->get();

        $count_jdk = count($jadwal);
        
        if ( $count_jdk > 0 ) {

            $no = 1;

            foreach( $jadwal as $jdk ) {

                // Kapasitas kelas cukup
                    if ( ($jdk->terisi + $jdk->akan_diisi) < $jdk->kapasitas_kls ) {
                        
                        // Cek bentrok jam
                        $bentrok = DB::table('krs_tmp as krs')
                                    ->leftJoin('jadwal_kuliah as jdk', 'jdk.id', 'krs.id_jdk')
                                    ->where('jdk.hari', $jdk->hari)
                                    ->where('jdk.id_jam', $jdk->id_jam)
                                    ->where('krs.id_mhs_reg', $r->id_mhs_reg)
                                    ->where('krs.id_smt', Sia::sessionPeriode())
                                    ->count();

                        if ( $bentrok > 0 ) {

                            if ( $no++ == $count_jdk ) {

                                return Response::json(['error' => 1, 'msg' => 'Tidak bisa menyesuaikan bentrokan jadwal, coba hapus matakuliah yang lain dan ambil matakuliah ini terlebih dahulu. Atau hubungi bagian akademik']);

                            } else {
                                continue;
                            }

                        } else {

                            if ( !$this->simpanTmp($jdk->id, $r->id_mhs_reg, $r->sks) ) {
                                return Response::json(['error' => 1, 'msg' => 'Maksimal SKS yang dapat diprogram telah melebihi batas maksimal']);
                            }

                        }

                        break;

                // Kapasitas kelas tidak cukup
                    } else {

                        if ( $no++ == $count_jdk ) {
                            
                            if ( !$this->simpanTmp($jdk->id, $r->id_mhs_reg, $r->sks) ) {
                                return Response::json(['error' => 1, 'msg' => 'Maksimal SKS yang dapat diprogram telah melebihi batas maksimal']);
                            }
                            break;

                        } else {
                            continue;
                        }
                    }
            }
        
        } else {
            return Response::json(['error' => 1, 'msg' => 'Terjadi kesalahan, muat ulang halaman dan ulangi lagi. Apabila masih belum bisa segera hubungi bagian akademik'], 200);
        }
    }
    
    public function deleteTmp($id)
    {
        DB::table('krs_tmp')->where('id', $id)->delete();
        Rmt::success('Berhasil menghapus matakuliah');
        return redirect()->back();
    }

	public function store(Request $r)
	{
        try {

            DB::transaction(function()use($r){

                $id_mhs_reg = Sia::sessionMhs('id_mhs_reg');
                $krs_tmp = DB::table('krs_tmp')
                            ->where('id_mhs_reg', $id_mhs_reg)
                            ->where('id_smt', Sia::sessionPeriode())
                            ->get();

                $jenis = $r->jenis;
                $data = [];

                foreach( $krs_tmp as $krs ) {
                    $mk = DB::table('jadwal_kuliah as jdk')
                            ->leftJoin('mk_kurikulum as mkur', 'jdk.id_mkur', 'mkur.id')
                            ->select('mkur.smt','jdk.jenis')
                            ->where('jdk.id', $krs->id_jdk)
                            ->first();

                    $cek = DB::table('nilai')
                            ->where('id_mhs_reg', $id_mhs_reg)
                            ->where('id_jdk', $krs->id_jdk)->count();

                    if ( $cek > 0 ) continue;

                    $data[] = ['id' => Rmt::uuid(), 'id_mhs_reg' => $id_mhs_reg, 'id_jdk' => $krs->id_jdk, 'semester_mk' => $mk->smt];
                }

                $jenis = $jenis == 1 ? 'KULIAH' : 'SP';

                // Simpan krs
                DB::table('nilai')->insert($data);

                // Kunci krs
                DB::table('krs_status')
                    ->where('id_smt', Sia::sessionPeriode())
                    ->where('id_mhs_reg', $id_mhs_reg)
                    ->where('jenis', $jenis)
                    ->update(['status_krs' => '1']);

                // Hapus isi krs_tmp
                // DB::table('krs_tmp')
                //     ->where('id_mhs_reg', $id_mhs_reg)
                //     ->where('id_smt','<>',Sia::sessionPeriode())
                //     ->delete();

                Rmt::success('Berhasil menyimpan data');

            });

        } catch(\Exception $e) {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()]);
        }
	}
}
