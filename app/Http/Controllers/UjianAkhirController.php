<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use DB, Sia, Rmt, Response, Session, Carbon, Excel;
use App\Mahasiswareg, App\Penguji, App\Ujianakhir, App\Seminar;

class UjianAkhirController extends Controller
{

    public function index(Request $r)
    {

        if ( !empty($r->jenis) ) {
            Session::set('ua_jenis', $r->jenis);
            if ( Session::get('ua_jenis') == 'P' ) {
                Session::set('ua_nm_jenis', 'Seminar Proposal');
            } elseif ( Session::get('ua_jenis') == 'H' ) {
                Session::set('ua_nm_jenis', 'Seminar Hasil');
            } elseif ( Session::get('ua_jenis') == 'S' ) {
                Session::set('ua_nm_jenis', 'Skripsi/Tesis/Disertasi');
            }
        }


        if ( !empty($r->smt) ) {
            Session::set('ua_semester', $r->smt);
        }

        if ( !empty($r->prodi) ) {
            Session::set('ua_prodi', $r->prodi);
        }

        if ( !Session::get('ua_semester') ) {
            $this->setSessionFilter();
        }

        Session::pull('tab');

        // Ambil data berdasarkan jenis tab
        if ( $r->tab == 'b' ) {

            Session::put('tab', 'b');

            $data['mahasiswa'] = $this->butuhPenguji($r);

        } elseif ( $r->tab == 'c' ) {

            Session::put('tab', 'c');

            $data['mahasiswa'] = $this->menungguPersetujuan($r);

        } elseif ( $r->tab == 'd' ) {

            Session::put('tab', 'd');

            $data['mahasiswa'] = $this->siapSeminar($r);

        } else {

            Session::put('tab', 'all');

            $data['mahasiswa'] = $this->allData($r);

        }

        return view('ujian-akhir.index', $data);
    }

    private function allData($r)
    {
        $mhs = $this->ujianAKhir(Session::get('ua_jenis'), Session::get('ua_prodi'), Session::get('ua_semester'), $r->cari);

        $data_mhs = $mhs->orderBy('nim')->get();
        $data_mhs_arr = $data_mhs->toArray();

        // Session mhs yang memprogram untuk dipakai di jadwal seminar
        Session::pull('mhs_in_ujian_akhir');
        Session::put('mhs_in_ujian_akhir', $data_mhs_arr);

        $page = $r->page ? $r->page : 1;
        $perpage = 10;

        $slice = array_slice($data_mhs_arr, $perpage * ($page - 1), $perpage);

        $mahasiswa = new Paginator($slice, count($data_mhs), $perpage);

        return $mahasiswa->setPath($r->url());
    }

    private function butuhPenguji($r)
    {
        // Kondisi: Selesai Bimbingan dan belum ada pengujinya

        $mhs = $this->ujianAKhir(Session::get('ua_jenis'), Session::get('ua_prodi'), Session::get('ua_semester'), $r->cari_b);

        $data_mhs = $mhs->orderBy('nim')->get();
        $data_mhs_arr = $data_mhs->toArray();

        $page = $r->page ? $r->page : 1;
        $perpage = 10;

        $slice = array_slice($data_mhs_arr, $perpage * ($page - 1), $perpage);
        $mahasiswa = new Paginator($slice, count($data_mhs), $perpage);

        return $mahasiswa->setPath($r->url());
    }

    private function menungguPersetujuan($r)
    {
        // Kondisi: Ada jam, tidak ada ruangan
        $mhs = $this->ujianAKhir(Session::get('ua_jenis'), Session::get('ua_prodi'), Session::get('ua_semester'), $r->cari_c);

        $data_mhs = $mhs->orderBy('nim')->get();
        $data_mhs_arr = $data_mhs->toArray();

        $page = $r->page ? $r->page : 1;
        $perpage = 10;

        $slice = array_slice($data_mhs_arr, $perpage * ($page - 1), $perpage);
        $mahasiswa = new Paginator($slice, count($data_mhs), $perpage);

        return $mahasiswa->setPath($r->url());
    }

    private function siapSeminar($r)
    {
        // Kondisi: Telah diinput ruangannya atau
        // Dengan persetujuan semua dosen pembimbing & penguji, keuangan, NDC (Khsusu hasil)

        $mhs = $this->ujianAKhir(Session::get('ua_jenis'), Session::get('ua_prodi'), Session::get('ua_semester'), $r->cari_d);

        $data_mhs = $mhs->orderBy('nim')->get();
        $data_mhs_arr = $data_mhs->toArray();

        $page = $r->page ? $r->page : 1;
        $perpage = 10;

        $slice = array_slice($data_mhs_arr, $perpage * ($page - 1), $perpage);
        $mahasiswa = new Paginator($slice, count($data_mhs), $perpage);

        return $mahasiswa->setPath($r->url());
    }
    private function ujianAkhir($jenis, $id_prodi, $smt, $cari = '')
    {

        $non_penguji = ['X'];

        // Tab butuh penguji
            if ( Session::get('tab') == 'b' || Session::get('tab') == 'c' ) {
                // Ambil bimbingan yang selesai
                $mhs_bimbingan = DB::table('bimbingan_mhs as bm')
                                ->leftJoin('mahasiswa_reg as m1', 'bm.id_mhs_reg', 'm1.id')
                                ->where('bm.pembimbing_1', '1')
                                ->where('bm.pembimbing_2', '1')
                                ->where('bm.id_smt', $smt)
                                ->where('bm.jenis', $jenis)
                                ->where('m1.id_prodi', $id_prodi)
                                ->pluck('bm.id_mhs_reg')
                                ->toArray();
                // dd($mhs_bimbingan);

                if ( !empty($mhs_bimbingan) ) {

                    $mhs_bimbingan = implode("','", $mhs_bimbingan);
                    $mhs_bimbingan = "'".$mhs_bimbingan."'";

                    // Ambil mahasiswa tanpa penguji dari $mhs_bimbingan
                    $query_non_penguji = "SELECT id_mhs_reg FROM 
                                            (SELECT 
                                                id_mhs_reg, (SELECT count(*) as jml_penguji from penguji as p2
                                                    where id_mhs_reg = p1.id_mhs_reg
                                                    and id_smt = p1.id_smt
                                                    and jenis = p1.jenis) as jml_penguji
                                                from penguji as p1
                                                    where id_mhs_reg in (".$mhs_bimbingan.")
                                                    and id_smt = $smt
                                                    and jenis = '".$jenis."'
                                                    group by id_mhs_reg) as res
                                            where jml_penguji < 3";

                    $sql_non_penguji = DB::select($query_non_penguji);

                    foreach( $sql_non_penguji as $val ) {
                        $non_penguji[] = $val->id_mhs_reg;
                    }
                    $non_penguji = collect($non_penguji);
                }

            }

        // Tab menunggu persetujuan
            if ( Session::get('tab') == 'c' ) {
                // Mhs yang menunggu persetujuan adalah mhs yang belum dinyatakan valid oleh keuangan
                // dan belum valid NDC (Jika seminar proposal) dan belum diisi ruangannya oleh prodi atau
                // Belum disetujui oleh pembimbing & penguji


                // Ambil data validasi bauk yang belum disetujui 
                $validasi_bauk = DB::table('seminar_pendaftaran')
                                    ->where('id_smt', $smt)
                                    ->where('jenis', $jenis)
                                    ->where('validasi_bauk', '0')
                                    ->whereNotIn('id_mhs_reg', $non_penguji)
                                    ->pluck('id_mhs_reg');
// dd($validasi_bauk);
                // Mahasiswa yg Tidak kosong jadwalnya tapi kosong ruangan
                $menunggu = DB::table('ujian_akhir')
                            ->where(function($q){
                                $q->whereNotNull('tgl_ujian')
                                    ->orWhere('tgl_ujian','<>', '');
                            })
                            ->where(function($q){
                                $q->whereNull('ruangan')
                                    ->orWhere('ruangan','');
                            })
                            ->whereNotIn('id_mhs_reg', $non_penguji)
                            ->where('id_smt', $smt)
                            ->where('jenis', $jenis)
                            ->pluck('id_mhs_reg');
// dd($menunggu);
                $menunggu_persetujuan = $menunggu->merge($validasi_bauk);

                if ( $jenis == 'H' ) {

                    // Mahasiswa yang belum valid oleh NDC
                    $valid_ndc = DB::table('seminar_pendaftaran')
                                    ->where('id_smt', $smt)
                                    ->where('jenis', $jenis)
                                    ->where('validasi_ndc', '0')
                                    ->pluck('id_mhs_reg');

                    $menunggu_persetujuan = $menunggu_persetujuan->merge($valid_ndc);

                }
            }

        // Siap seminar
            // Mhs yang siap seminar adalah adalah mhs yang telah dinyatakan valid oleh keuangan
            // dan valid NDC (Jika seminar proposal) dan telah diisi ruangannya oleh prodi atau
            // Telah disetujui oleh pembimbing & penguji

            $siap_seminar = collect(['X']);
            $valid_bauk_nidc = collect(['X']);

            if ( Session::get('tab') == 'd' ) {

                
                // Ambil data validasi bauk yang telah disetujui
                $val_validasi_bauk = collect(['X']);
                $validasi_bauk = DB::table('seminar_pendaftaran')
                                    ->where('id_smt', $smt)
                                    ->where('jenis', $jenis)
                                    ->where('validasi_bauk', '1')
                                    ->pluck('id_mhs_reg');
                if ( $validasi_bauk->count() != 0 ) {
                    $val_validasi_bauk = $validasi_bauk;
                }

                $valid_bauk_nidc = $val_validasi_bauk;


                if ( $jenis == 'H' ) {

                    // Ambil data validasi NDC yang telah disetujui
                    $val_validasi_ndc = collect(['X']);
                    $validasi_ndc = DB::table('seminar_pendaftaran')
                                        ->where('id_smt', $smt)
                                        ->where('jenis', $jenis)
                                        ->where('validasi_ndc', '1')
                                        ->pluck('id_mhs_reg');
                    if ( $validasi_ndc->count() != 0 ) {
                        $val_validasi_ndc = $validasi_ndc;
                    }

                    // Gabung hasil dari Bauk dan NIDC
                    $valid_bauk_nidc = $val_validasi_ndc->merge($val_validasi_bauk);

                }

                // dd($valid_bauk_nidc);
                // Ambil semua data dengan isi field siap_seminar = 1 & telah disetujui keuangan & nidc (jika hasil)
                // Field siap_seminar hanya bisa diisi oleh prodi, terisi saat memasukkan ruangan
                $cek_siap_seminar = DB::table('ujian_akhir')
                                    ->where('id_smt', $smt)
                                    ->where('jenis', $jenis)
                                    ->where('siap_seminar', '1')
                                    // ->whereIn('id_mhs_reg', $valid_bauk_nidc)
                                    ->pluck('id_mhs_reg');

                $siap_seminar = $cek_siap_seminar;
                // dd($siap_seminar);
            }

        
        // Ambil mk lama (seminar proposal dan hasil)
        $kur_lama = DB::table('nilai as n')
                ->leftJoin('mahasiswa_reg as m1', 'n.id_mhs_reg','=','m1.id')
                ->leftJoin('mahasiswa as m2', 'm1.id_mhs','=','m2.id')
                ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
                ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
                ->leftJoin('matakuliah as mk', 'mkur.id_mk','=','mk.id')
                ->where('mk.ujian_akhir', $jenis)
                ->where('jdk.id_prodi', $id_prodi)
                ->where('jdk.id_smt', $smt)
                ->pluck('n.id_mhs_reg');

        // Telah ada nilai di semester sebelumnya
        $smt_minus_3 = Rmt::smtMinus($smt, 3); // Ambil 3 periode lalu
        $has_nilai = DB::table('penguji')
                        ->where('jenis', $jenis)
                        ->whereIn('id_smt', $smt_minus_3)
                        ->where(function($q){
                            $q->whereNotNull('nilai')
                                ->orWhere('nilai', '');
                        })
                        ->groupBy('id_mhs_reg')
                        ->pluck('id_mhs_reg');

        // Main query
        $query2 = DB::table('nilai as n')
            ->leftJoin('mahasiswa_reg as m1', 'n.id_mhs_reg','=','m1.id')
            ->leftJoin('mahasiswa as m2', 'm1.id_mhs','=','m2.id')
            ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
            ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
            ->leftJoin('matakuliah as mk', 'mkur.id_mk','=','mk.id')
            ->select('jdk.id as id_jdk','m1.nim','m1.id as id_mhs_reg', 
                'm2.nm_mhs', 'jdk.id_smt','jdk.id_prodi','n.nilai_huruf','n.nil_mid','n.nil_final','mk.sks_mk',
                DB::raw('(select group_concat(distinct d.gelar_depan," ", d.nm_dosen,", ", d.gelar_belakang SEPARATOR \'<br>\') as d from penguji as p 
                        left join dosen as d on p.id_dosen = d.id
                        where p.id_mhs_reg=m1.id
                        and p.id_smt=\''.$smt.'\'
                        AND p.jabatan=\'KETUA\'
                        AND p.jenis=\''.$jenis.'\') as pembimbing'))
            ->where('mk.ujian_akhir', 'S')
            ->where('jdk.id_prodi', $id_prodi)
            ->where('jdk.id_smt', $smt)
            ->whereNotIn('n.id_mhs_reg', $kur_lama)
            ->whereNotIn('n.id_mhs_reg', $has_nilai);

            if ( !empty($cari) ) {
                $query2->where(function($q)use($cari){
                    $q->where('m1.nim', 'like', '%'.$cari.'%')
                        ->orWhere('m2.nm_mhs', 'like', '%'.$cari.'%');
                });
            }

            if ( Session::get('tab') == 'b') {
                $query2->whereIn('n.id_mhs_reg', $non_penguji);
            }

            if ( Session::get('tab') == 'c') {
                $query2->whereIn('n.id_mhs_reg', $menunggu_persetujuan);
            }

            if ( Session::get('tab') == 'd') {
                $query2->whereIn('n.id_mhs_reg', $siap_seminar);
            }

        // Main query
        $query = DB::table('nilai as n')
                ->leftJoin('mahasiswa_reg as m1', 'n.id_mhs_reg','=','m1.id')
                ->leftJoin('mahasiswa as m2', 'm1.id_mhs','=','m2.id')
                ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
                ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
                ->leftJoin('matakuliah as mk', 'mkur.id_mk','=','mk.id')
                ->select('jdk.id as id_jdk','m1.nim','m1.id as id_mhs_reg', 
                    'm2.nm_mhs', 'jdk.id_smt','jdk.id_prodi','n.nilai_huruf','n.nil_mid','n.nil_final','mk.sks_mk',
                    DB::raw('(select group_concat(distinct d.gelar_depan," ", d.nm_dosen,", ", d.gelar_belakang SEPARATOR \'<br>\') as d from penguji as p 
                            left join dosen as d on p.id_dosen = d.id
                            where p.id_mhs_reg=m1.id
                            AND p.jabatan=\'KETUA\'
                            AND p.jenis=\''.$jenis.'\') as pembimbing'))
                ->where('mk.ujian_akhir', $jenis)
                ->where('jdk.id_prodi', $id_prodi)
                ->where('jdk.id_smt', $smt);

            if ( !empty($cari) ) {
                $query->where(function($q)use($cari){
                    $q->where('m1.nim', 'like', '%'.$cari.'%')
                        ->orWhere('m2.nm_mhs', 'like', '%'.$cari.'%');
                });
            }

            if ( Session::get('tab') == 'b' ) {

                $query->whereIn('n.id_mhs_reg', $non_penguji);
            }

            if ( Session::get('tab') == 'c') {
                $query->whereIn('n.id_mhs_reg', $menunggu_persetujuan);
            }

            if ( Session::get('tab') == 'd') {
                $query->whereIn('n.id_mhs_reg', $siap_seminar);
            }
        $query->union($query2);

        return $query;

    }

    private function setSessionFilter()
    {
        Session::set('ua_semester', Sia::sessionPeriode());
        Session::set('ua_jenis', 'P');
        $prodi_user = Sia::getProdiUser();
        Session::set('ua_prodi', @$prodi_user[0]);
    }

    public function penguji(Request $r)
    {
        $mhs = Mahasiswareg::find($r->id_mhs_reg); 
        $jenis = Session::get('ua_jenis');

        $ujian = DB::table('ujian_akhir')
                        ->where('id_mhs_reg', $r->id_mhs_reg)
                        ->where('jenis', Session::get('ua_jenis'))
                        ->where('id_smt', Session::get('ua_semester'))
                        ->first();

        $judul = !empty($ujian) ? $ujian->judul_tmp : '';
        // $id_update = !empty

        $ketua = Sia::penguji($r->id_mhs_reg, Session::get('ua_jenis'), 'KETUA', Session::get('ua_semester'));
        if ( empty($ketua) ) {
            $ketua = Sia::penguji($r->id_mhs_reg, Session::get('ua_jenis'), 'KETUA');
        }

        $sekretaris = Sia::penguji($r->id_mhs_reg, Session::get('ua_jenis'), 'SEKRETARIS', Session::get('ua_semester'));
        if ( empty($sekretaris) ) {
            $sekretaris = Sia::penguji($r->id_mhs_reg, Session::get('ua_jenis'), 'SEKRETARIS');
        }

        $anggota = Sia::penguji($r->id_mhs_reg, Session::get('ua_jenis'), 'ANGGOTA', Session::get('ua_semester'));
        if ( empty($anggota) ) {
            $anggota = Sia::penguji($r->id_mhs_reg, Session::get('ua_jenis'), 'ANGGOTA');
        }


        $pukul = !empty($ujian) ? explode(' - ', $ujian->pukul) : [];
        if ( count($pukul) != 2 ) {
            $pukul = ['--', '--'];
        }

        $disable_penguji = "";
        if ( $r->tab == 'c' ) {
            $disable_penguji = "disabled=''";
        }

        ?>

        <input type="hidden" name="id_mhs_reg" value="<?= $r->id_mhs_reg ?>">

        <div class="table-responsive">
            <table border="0" class="table table-hover table-form">
                <tr>
                    <td width="160px">NIM</td>
                    <td><?= $r->nim ?></td>
                </tr>
                <tr>
                    <td>Nama</td>
                    <td><?= $r->nama ?></td>
                </tr>
                <tr><td colspan="2"><br><b>Data dosen penguji</b></td></tr>
                <tr>
                    <td>Ketua (Pemb. I)<span>*</span></td>
                    <td>
                            <div style="position: relative;">
                                <div class="input-icon right"> 
                                    <span id="spinner-autocomplete-1" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                    <input type="text" class="form-control" id="autocomplete-ajax-1" value="<?= !empty($ketua) ? $ketua->nm_dosen : '' ?>" <?= $disable_penguji ?>>
                                    <input type="hidden" id="ketua" value="<?= !empty($ketua) ? $ketua->id : '' ?>" name="ketua">
                                </div>
                            </div>
                    </td>
                </tr>
                <tr>
                    <td>Sekretaris (Pemb. II)<span>*</span></td>
                    <td>
                            <div style="position: relative;">
                                <div class="input-icon right"> 
                                    <span id="spinner-autocomplete-2" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                    <input type="text" class="form-control" id="autocomplete-ajax-2" value="<?= !empty($sekretaris) ? $sekretaris->nm_dosen : '' ?>" <?= $disable_penguji ?>>
                                    <input type="hidden" id="sekretaris" name="sekretaris" value="<?= !empty($sekretaris) ? $sekretaris->id : '' ?>">
                                </div>
                            </div>
                    </td>
                </tr>
                <tr>
                    <td>Anggota</td>
                    <td>
                        <div style="position: relative;">
                            <div class="input-icon right"> 
                                <span id="spinner-autocomplete-3" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                <input type="text" class="form-control" id="autocomplete-ajax-3" value="<?= !empty($anggota) ? $anggota->nm_dosen : '' ?>" <?= $disable_penguji ?>>
                                <input type="hidden" id="anggota" name="anggota" value="<?= !empty($anggota) ? $anggota->id : '' ?>">
                            </div>
                        </div>
                    </td>
                </tr>

                <?php 
                // if ( Session::get('ua_prodi') == '61101' ) {
                ?>
                    <?php 
                        $anggota2 = Sia::penguji($r->id_mhs_reg, Session::get('ua_jenis'), 'ANGGOTA2', Session::get('ua_semester'));
                        if ( empty($anggota2) ) {
                            $anggota2 = Sia::penguji($r->id_mhs_reg, Session::get('ua_jenis'), 'ANGGOTA2');
                        }
                    ?>
                    <tr>
                        <td width="160px">Anggota 2</td>
                        <td>
                            <div style="position: relative;">
                                <div class="input-icon right"> 
                                    <span id="spinner-autocomplete-4" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                    <input type="text" class="form-control" id="autocomplete-ajax-4" value="<?= !empty($anggota2) ? $anggota2->nm_dosen : '' ?>" <?= $disable_penguji ?>>
                                    <input type="hidden" id="anggota-2" name="anggota_2" value="<?= !empty($anggota2) ? $anggota2->id : '' ?>">
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php 
                // }

                ?>

                <tr><td colspan="2"><br><b>Jadwal Ujian</b></td></tr>

                <tr>
                    <td>Tanggal</td>
                    <td><input type="date" name="tgl" class="form-control mw-2" value="<?= empty($ujian->tgl_ujian) ? '' : $ujian->tgl_ujian ?>"></td>
                </tr>

                <tr>
                    <td>Pukul</td>
                    <td>
                        <select name="pukul_1" class="form-custom mw-2" onchange="getEnd(this.value)">
                            <option value="">--</option>
                            <?php foreach( Rmt::pukul() as $key => $val ) { ?>
                                <option value="<?= $key ?>" <?= $pukul[0] == $val ? 'selected':'' ?>><?= $val ?></option>
                            <?php } ?>
                        </select>
                        -
                        <select name="pukul_2" class="form-custom mw-2" id="pukul-2">
                            <option value="">--</option>
                            <?php foreach( Rmt::pukul() as $key => $val ) { ?>
                                <option value="<?= $key ?>" <?= $pukul[1] == $val ? 'selected':'' ?>><?= $val ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Ruangan</td>
                    <td><input type="text" name="ruangan" class="form-control mw-2" value="<?= empty($ujian->ruangan) ? '' : $ujian->ruangan ?>"></td>
                </tr>
                <tr>
                    <td>Judul</td>
                    <td>
                        <textarea name="judul" rows="4" class="form-control"><?= $judul ?></textarea>
                    </td>
                </tr>

                <?php 

                // Jika 
                if ( $r->tab == 'c' ) {

                    $persetujuan_pbb = DB::table('penguji')
                                        ->where('id_mhs_reg', $mhs->id)
                                        ->where('jenis', $jenis)
                                        ->where('id_smt', Session::get('ua_semester'))
                                        ->select('jabatan', 'setuju')
                                        ->get();

                    $data_seminar = DB::table('seminar_pendaftaran')
                                    ->where('id_mhs_reg', $mhs->id)
                                    ->where('id_smt', Session::get('ua_semester'))
                                    ->where('jenis', $jenis)
                                    ->select('validasi_bauk', 'validasi_ndc')
                                    ->first();

                    $jabatan_arr = [
                        'KETUA' => 'Pembimbing I',
                        'SEKRETARIS' => 'Pembimbing II',
                        'ANGGOTA' => 'Penguji I',
                        'ANGGOTA2' => 'Penguji II',
                    ]; ?>

                    <tr><td colspan="2"><br><b>Persetujuan Seminar</b></td></tr>
                    <?php $no_ = 1; ?>
                    <?php foreach( $persetujuan_pbb as $pbb ) { ?>
                        <tr>
                            <td><?= $jabatan_arr[$pbb->jabatan] ?></td>
                            <td>
                                <?= $pbb->setuju == 0 ? '<i class="fa fa-times"></i> Belum disetujui' : '<i class="fa fa-check"  style="color: green"></i> Disetujui' ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td>Keuangan</td>
                        <td><?= $data_seminar->validasi_bauk == 0 ? '<i class="fa fa-times"></i> Belum disetujui' : '<i class="fa fa-check" style="color: green"></i> Disetujui' ?></td>
                    </tr>

                    <?php if ( Session::get('ua_jenis') == 'H' ) { ?>
                        <tr>
                            <td>NDC</td>
                            <td><?= $data_seminar->validasi_ndc == 0 ? '<i class="fa fa-times"></i> Belum disetujui' : '<i class="fa fa-check" style="color: green"></i> Disetujui' ?></td>
                        </tr>
                    <?php } ?>

                <?php } ?>

            </table>
        </div>

        <script type="text/javascript" src="<?= url('resources') ?>/assets/js/jquery.autocomplete.js"></script>
        <script type="text/javascript" src="<?= url('resources') ?>/assets/js/jquery.mockjax.js"></script>
        <script>
            function getEnd(value)
            {
                var next = 0;
                var next = parseInt(value) + 1;
                $('#pukul-2').val(next);

            }

            $(function(){

                // Penguji 1
                    $('#autocomplete-ajax-1').autocomplete({
                        serviceUrl: '<?= route('jdk_dosen') ?>',
                        lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                            var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                            return re.test(suggestion.value);
                        },
                        onSearchStart: function(data) {
                            $('#spinner-autocomplete-1').show();
                        },
                        onSearchComplete: function(data) {
                            $('#spinner-autocomplete-1').hide();
                        },
                        onSelect: function(suggestion) {
                            $('#ketua').val(suggestion.data);
                        },
                        onInvalidateSelection: function() {
                        }
                    });

                // Penguji 2
                    $('#autocomplete-ajax-2').autocomplete({
                        serviceUrl: '<?= route('jdk_dosen') ?>',
                        lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                            var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                            return re.test(suggestion.value);
                        },
                        onSearchStart: function(data) {
                            $('#spinner-autocomplete-2').show();
                        },
                        onSearchComplete: function(data) {
                            $('#spinner-autocomplete-2').hide();
                        },
                        onSelect: function(suggestion) {
                            $('#sekretaris').val(suggestion.data);
                        },
                        onInvalidateSelection: function() {
                        }
                    });

                // Penguji 3
                    $('#autocomplete-ajax-3').autocomplete({
                        serviceUrl: '<?= route('jdk_dosen') ?>',
                        lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                            var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                            return re.test(suggestion.value);
                        },
                        onSearchStart: function(data) {
                            $('#spinner-autocomplete-3').show();
                        },
                        onSearchComplete: function(data) {
                            $('#spinner-autocomplete-3').hide();
                        },
                        onSelect: function(suggestion) {
                            $('#anggota').val(suggestion.data);
                        },
                        onInvalidateSelection: function() {
                        }
                    });


                    // Penguji 4
                    $('#autocomplete-ajax-4').autocomplete({
                        serviceUrl: '<?= route('jdk_dosen') ?>',
                        lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                            var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                            return re.test(suggestion.value);
                        },
                        onSearchStart: function(data) {
                            $('#spinner-autocomplete-4').show();
                        },
                        onSearchComplete: function(data) {
                            $('#spinner-autocomplete-4').hide();
                        },
                        onSelect: function(suggestion) {
                            $('#anggota-2').val(suggestion.data);
                        },
                        onInvalidateSelection: function() {
                        }
                    });
            });
        </script>
        <?php
    }

    public function pengujiStore(Request $r)
    {
        $this->validate($r, [
            'ketua' => 'required',
            'sekretaris' => 'required',
        ]);

        $save_jadwal = true;

        if ( $r->pukul_1 === '' || $r->pukul_2 === '' ) {
            $save_jadwal = false;
        }


        if ( $save_jadwal && ( $r->pukul_1 > $r->pukul_2 ) ) {
            return Response::json(['error' => 1,'msg'=> 'Jam mulai lebih besar dari jam selesai']);
        }
        try {

            $semester       = Session::get('ua_semester');
            $jenis_seminar  = Session::get('ua_jenis');

            // $dosen_pembimbing = [$r->ketua, $r->sekretaris];
            // $this->updateValidasiSeminar($r->id_mhs_reg, $jenis_seminar, $semester, $dosen_pembimbing);

            DB::beginTransaction();


                if ( $save_jadwal ) {
                    $pukul = Rmt::pukul($r->pukul_1).' - '.Rmt::pukul($r->pukul_2);
                }

                $data[] = ['id_smt' => $semester, 'id_mhs_reg' => $r->id_mhs_reg, 'id_dosen' => $r->ketua, 'jabatan' => 'KETUA'];
                $data[] = ['id_smt' => $semester, 'id_mhs_reg' => $r->id_mhs_reg, 'id_dosen' => $r->sekretaris, 'jabatan' => 'SEKRETARIS'];
                $data[] = ['id_smt' => $semester, 'id_mhs_reg' => $r->id_mhs_reg, 'id_dosen' => $r->anggota, 'jabatan' => 'ANGGOTA'];
                $data[] = ['id_smt' => $semester, 'id_mhs_reg' => $r->id_mhs_reg, 'id_dosen' => $r->anggota_2, 'jabatan' => 'ANGGOTA2'];

                for ( $i = 0; $i < 4; $i++ ) {


                    // Jika penguji kosong (Field penguji belum diisi semua)
                    if ( empty($data[$i]['id_dosen']) ) continue;

                    // Jika tidak kosong data jadwal seminar
                    if ( $save_jadwal ) {
                        // Cek penguji yg tabrakan jam
                        $cek = DB::table('ujian_akhir as ua')
                                ->join('penguji as p', function($join) {
                                    $join->on('ua.id_mhs_reg', '=', 'p.id_mhs_reg');
                                    $join->on('ua.jenis', '=', 'p.jenis');
                                })
                                // ->where('ua.jenis', $jenis_seminar)
                                ->where('ua.pukul', $pukul)
                                ->where('ua.tgl_ujian', $r->tgl)
                                ->where('ua.id_smt', $semester)
                                ->where('p.id_dosen', $data[$i]['id_dosen'])
                                ->where('ua.id_mhs_reg','<>', $r->id_mhs_reg)
                                ->count();

                        if ( $cek > 0 ) {
                            $dosen = DB::table('dosen')->where('id', $data[$i]['id_dosen'])->first();
                            return Response::json([$dosen->nm_dosen.' menguji pada jam tersebut.'], 422);
                        }
                    }
                        

                    Penguji::updateOrCreate([ 
                                'id_smt' => $semester,
                                'id_mhs_reg' => $r->id_mhs_reg,
                                'jenis' => $jenis_seminar,
                                'jabatan' => $data[$i]['jabatan'],
                            ], $data[$i]);

                    
                    // Simpan juga penguji untuk hasil & tesis saat penguji proposal diinput
                    if ( $jenis_seminar == 'P' ) {
                        Penguji::updateOrCreate([
                                'id_smt' => $semester,
                                'id_mhs_reg' => $r->id_mhs_reg,
                                'jenis' => 'H',
                                'jabatan' => $data[$i]['jabatan'],
                            ], $data[$i]);
                        Penguji::updateOrCreate([
                                'id_smt' => $semester,
                                'id_mhs_reg' => $r->id_mhs_reg,
                                'jenis' => 'S',
                                'jabatan' => $data[$i]['jabatan'],
                            ], $data[$i]);

                    }

                    // Simpan semester + 1 (contoh: 20211 + 1), untuk mengantisipasi error bimbingan jika hanya proposal yang selesai semester ini
                    $this->storePengujiFuture($data[$i], $jenis_seminar, $semester);

                }


                if ( $save_jadwal ) {

                    // Ujian Akhir
                    $data_ua = [
                        'id_smt' => $semester,
                        'judul_tmp' => $r->judul, 
                        'tgl_ujian' => $r->tgl,
                        'pukul' => $pukul,
                        'ruangan' => $r->ruangan,
                        'jenis' => $jenis_seminar,
                        'siap_seminar' => '1'];

                } else {

                    // Ujian Akhir
                    $data_ua = [
                        'id_smt' => $semester,
                        'judul_tmp' => $r->judul,
                        'jenis' => $jenis_seminar];

                }

                $a = Ujianakhir::updateOrCreate([
                    'id_smt' => $semester,
                    'id_mhs_reg' => $r->id_mhs_reg,
                    'jenis' => $jenis_seminar,
                ], $data_ua);

                $arr_jenis = ['P', 'H', 'S'];

                foreach( $arr_jenis as $val )
                {
                    // Insert jika belum ada di tabel ujian akhir
                    Ujianakhir::firstOrCreate([
                        'id_smt' => $semester,
                        'id_mhs_reg' => $r->id_mhs_reg,
                        'jenis' => $val,
                    ]);
                }

                // Update validasi seminar
                // $dosen_pembimbing = [$r->ketua, $r->sekretaris];
                // $this->updateValidasiSeminar($r->id_mhs_reg, $jenis_seminar, $semester, $dosen_pembimbing);

            DB::commit();

        } catch(\Exception $e) {

            DB::rollback();
            return Response::json([$e->getMessage()], 422);
        
        }

        Rmt::success('Berhasil menyimpan data');
        return Response::json(['error' => 0, 'msg' => '']);
    }   

    private function storePengujiFuture($data, $jenis, $smt)
    {

        try {

            $smt_plus = Rmt::smtPlus1($smt);
            // Rebuild data untuk menambah semester
            $data = [
                 'id_smt' => $smt_plus,
                 'id_mhs_reg' => $data['id_mhs_reg'],
                 'id_dosen' => $data['id_dosen'],
                 'jabatan' => $data['jabatan']
            ];

            Penguji::updateOrCreate([ 
                'id_smt' => $smt_plus,
                'id_mhs_reg' => $data['id_mhs_reg'],
                'jenis' => $jenis,
                'jabatan' => $data['jabatan'],
            ], $data);

            // Simpan juga penguji untuk hasil & tesis saat penguji proposal diinput
            if ( $jenis == 'P' ) {

                Penguji::updateOrCreate([ 
                        'id_smt' => $smt_plus,
                        'id_mhs_reg' => $data['id_mhs_reg'],
                        'jenis' => 'H',
                        'jabatan' => $data['jabatan'],
                    ], $data);
                Penguji::updateOrCreate([ 
                        'id_smt' => $smt_plus,
                        'id_mhs_reg' => $data['id_mhs_reg'],
                        'jenis' => 'S',
                        'jabatan' => $data['jabatan'],
                    ], $data);

            }

        } catch( \Exception $e ) {
            abort(422, $e->getMessage());
        }
    }

    private function updateValidasiSeminar($id_mhs_reg, $jenis, $id_smt, $dosen_pembimbing)
    {

        try {

            $kode_seminar = ['P' => 1, 'H' => 2, 'S' => 3];

            // Ambil data pendaftaran seminar
            $seminar = Seminar::where('id_mhs_reg', $id_mhs_reg)
                        ->where('jenis', $kode_seminar[$jenis])
                        ->where('id_smt', $id_smt)
                        ->first();

            if ( is_object($seminar) ) {

                $validasi = DB::table('seminar_validasi')
                                ->where('id_seminar', $seminar->id)
                                ->orderBy('id', 'asc')
                                ->get();

                foreach( $validasi as $key => $val ) {

                    // Update hanya jika pembimbing diganti
                    if ( $dosen_pembimbing[$key] != $val->id_dosen ) {

                        DB::table('seminar_validasi')
                            ->where('id', $val->id)
                            ->update([
                                'id_dosen' => $dosen_pembimbing[$key]
                            ]);

                    }
                }

            }

        } catch(\Exception $e) {
            abort(422, $e->getMessage());
        }
    }

    public function nilai(Request $r)
    {

        $mhs = Mahasiswareg::find($r->id_mhs_reg); 

        $ujian = DB::table('ujian_akhir')
                        ->where('id_mhs_reg', $r->id_mhs_reg)
                        ->where('jenis', Session::get('ua_jenis'))
                        ->first();

        $judul = !empty($ujian) ? $ujian->judul_tmp : '';

        $ketua = Sia::penguji($r->id_mhs_reg, Session::get('ua_jenis'), 'KETUA', Session::get('ua_semester'));
        $sekretaris = Sia::penguji($r->id_mhs_reg, Session::get('ua_jenis'), 'SEKRETARIS', Session::get('ua_semester'));
        $anggota = Sia::penguji($r->id_mhs_reg, Session::get('ua_jenis'), 'ANGGOTA', Session::get('ua_semester'));
        $anggota2 = Sia::penguji($r->id_mhs_reg, Session::get('ua_jenis'), 'ANGGOTA2', Session::get('ua_semester'));

        $ketua_nil = !empty($ketua->nilai) ? $ketua->nilai : 0;
        $sekretaris_nil = !empty($sekretaris->nilai) ? $sekretaris->nilai : 0;
        $anggota_nil = !empty($anggota->nilai) ? $anggota->nilai : 0;
        $anggota2_nil = !empty($anggota2->nilai) ? $anggota2->nilai : 0;

        $pembagi = 0;

        if ( !empty($ketua->nilai) ) {
            $pembagi += 1;
        }

        if ( !empty($sekretaris->nilai) ) {
            $pembagi += 1;
        }

        if ( !empty($anggota->nilai) ) {
            $pembagi += 1;
        }

        if ( !empty($anggota2->nilai) ) {
            $pembagi += 1;
        }
        
        if ( !empty($pembagi) ) {
            $average = number_format(($ketua_nil + $sekretaris_nil + $anggota_nil + $anggota2_nil) / $pembagi,2);
        } else {
            $average = 0;
        }
        $grade = $average.' : '.Sia::grade(Session::get('ua_prodi'), $average);
        ?>

        <input type="hidden" name="id_mhs_reg" value="<?= $r->id_mhs_reg ?>">
        <input type="hidden" name="id_jdk" value="<?= $r->id_jdk ?>">
        <?php if ( $mhs->id_prodi == '61101' && Session::get('ua_jenis') == 'S' ) { ?>
            <!-- jika mk seminar proposal & hasil tidak diprogramkan  -->
            <input type="hidden" name="skripsi" value="true">
        <?php } ?>

        <div class="table-responsive">
            <table border="0" class="table table-hover table-form">
                <tr>
                    <td width="100px">Mahasiswa </td>
                    <td colspan="2"><?= $r->nim ?> - <?= $r->nama ?></td>
                </tr>
                <tr>
                    <td>Tgl Ujian</td>
                    <td colspan="2">
                        <?= !empty($ujian->tgl_ujian) ? Carbon::parse($ujian->tgl_ujian)->format('d/m/Y') : '' ?>
                    </td>
                </tr>
                <tr>
                    <td>Judul</td>
                    <td colspan="2">
                        <?= $judul ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><br><b>Data dosen penguji</b></td>
                    <td align="center"><br><b>Nilai</b></td>
                </tr>
                <tr>
                    <td>Ketua <span>*</span></td>
                    <td width="220">
                        <input type="text" class="form-control" style="font-size: 12px" value="<?= !empty($ketua) ? $ketua->nm_dosen : '' ?>" disabled="">
                    </td>
                    <td width="40"><input type="text" id="nil_ketua" name="nil_ketua" class="form-control number" <?= empty($ketua) ? 'disabled=""':' value="'.$ketua->nilai.'"' ?>></td>
                </tr>
                <tr>
                    <td>Sekretaris <span>*</span></td>
                    <td>
                        <input type="text" class="form-control" style="font-size: 12px" value="<?= !empty($sekretaris) ? $sekretaris->nm_dosen : '' ?>" disabled="">
                    </td>
                    <td><input type="text" id="nil_sekretaris" name="nil_sekretaris" class="form-control number" <?= empty($sekretaris) ? 'disabled=""':' value="'.$sekretaris->nilai.'"' ?>></td>
                </tr>
                <tr>
                    <td>Anggota <span>*</span></td>
                    <td>
                        <input type="text" class="form-control" style="font-size: 12px" value="<?= !empty($anggota) ? $anggota->nm_dosen : '' ?>" disabled="">
                    </td>
                    <td><input type="text" id="nil_anggota" name="nil_anggota" class="form-control number" <?= empty($anggota) ? 'disabled=""':' value="'.$anggota->nilai.'"' ?>></td>

                </tr>

                <script>
                    (function ( $ ) {

                        $.fn.nilai = function( options ) {

                            var set = $.extend({
                                nil_ketua: 0,
                                nil_sekretaris: 0,
                                nil_anggota: 0,
                                nil_anggota2: 0,
                            }, options );

                            var total = this, angka, pembagi = 0;
                            var nil_ketua = 0, nil_sekretaris = 0, nil_anggota = 0, nil_anggota2 = 0;

                            this.html('<i class="fa fa-spinner fa-spin"></i>');

                                if ( set.nil_ketua != '' ) {
                                    nil_ketua = set.nil_ketua;
                                }
                                if ( set.nil_sekretaris != '' ) {
                                    nil_sekretaris = set.nil_sekretaris;
                                }
                                if ( set.nil_anggota != '' ) {
                                    nil_anggota = set.nil_anggota;
                                }
                                if ( set.nil_anggota2 != '' ) {
                                    nil_anggota2 = set.nil_anggota2;
                                }

                                if ( parseFloat(set.nil_ketua) >= 1 ) {
                                    pembagi = pembagi + 1;
                                }
                                if ( parseFloat(set.nil_sekretaris) >= 1 ) {
                                    pembagi = pembagi + 1;
                                }
                                if ( parseFloat(set.nil_anggota) >= 1 ) {
                                    pembagi = pembagi + 1;
                                }
                                if ( parseFloat(set.nil_anggota2) >= 1 ) {
                                    pembagi = pembagi + 1;
                                }

                                angka = parseFloat(nil_ketua)+parseFloat(nil_sekretaris)+parseFloat(nil_anggota)+parseFloat(nil_anggota2);
                                angka = angka/pembagi;

                            var nilai = angka.toFixed(2);
                            
                            $.ajax({
                                url: '<?= route('grade') ?>',
                                data: { prodi: '<?= Session::get('ua_prodi') ?>', nilai: nilai },
                                success: function(data){
                                    $(total).html(nilai);
                                    $(total).append(data);
                                },
                                error: function(err,data,msg)
                                {
                                    alert(msg)
                                }
                            });
                            
                        };
                     
                    }( jQuery ));

                    $(function(){
                        $('.number').keypress(function(event) {
                          if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                            event.preventDefault();
                          }
                        });
                    });
                </script>


                    <?php $anggota2 = Sia::penguji($r->id_mhs_reg, Session::get('ua_jenis'), 'ANGGOTA2', Session::get('ua_semester')); ?>
                    <tr>
                        <td>Anggota 2 <span>*</span></td>
                        <td>
                        <input type="text" class="form-control number" style="font-size: 12px" value="<?= !empty($anggota2) ? $anggota2->nm_dosen : '' ?>" disabled="">
                    </td>
                    <td><input type="text" id="nil_anggota2" name="nil_anggota2" class="form-control" <?= empty($anggota2) ? 'disabled=""':' value="'.$anggota2->nilai.'"' ?>></td>

                    </tr>

                    <tr>
                        <td align="center" colspan="2"></td>
                        <td id="total" align="center" style="font-weight: bold;font-size: 16px">
                            <?= $grade ?>
                        </td>
                    </tr>

                    <script>
                        $(function(){

                            var nil_ketua = $('#nil_ketua').val(),
                                nil_sekretaris = $('#nil_sekretaris').val(), 
                                nil_anggota = $('#nil_anggota').val(),
                                nil_anggota2 = $('#nil_anggota2').val();

                            $('#nil_ketua').blur(function(){
                                nil_ketua = $(this).val();

                                $('#total').nilai({
                                    nil_ketua : nil_ketua,
                                    nil_sekretaris : nil_sekretaris,
                                    nil_anggota : nil_anggota,
                                    nil_anggota2 : nil_anggota2
                                });
                            });

                            $('#nil_sekretaris').blur(function(){
                                nil_sekretaris = $(this).val();
                                
                                $('#total').nilai({
                                    nil_ketua : nil_ketua,
                                    nil_sekretaris : nil_sekretaris,
                                    nil_anggota : nil_anggota,
                                    nil_anggota2 : nil_anggota2
                                });
                            });

                            $('#nil_anggota').blur(function(){
                                nil_anggota = $(this).val();
                                $('#total').nilai({
                                    nil_ketua : nil_ketua,
                                    nil_sekretaris : nil_sekretaris,
                                    nil_anggota : nil_anggota,
                                    nil_anggota2 : nil_anggota2
                                });
                            });

                            $('#nil_anggota2').blur(function(){
                                nil_anggota2 = $(this).val();
                                
                                $('#total').nilai({
                                    nil_ketua : nil_ketua,
                                    nil_sekretaris : nil_sekretaris,
                                    nil_anggota : nil_anggota,
                                    nil_anggota2 : nil_anggota2
                                });
                            });
                            
                        });
                    </script>


                <!-- Untuk s2 -->
                <?php if ( $mhs->id_prodi == '61101' && Session::get('ua_jenis') == 'S' ) {
                    
                    $pembagi = 1;
                    $proposal = $this->hitungNilai($mhs->id, 'P');
                    $hasil = $this->hitungNilai($mhs->id, 'H');
                    if ( !empty($proposal) ) {
                        $pembagi += 1;
                    }
                    if ( !empty($hasil) ) {
                        $pembagi += 1;
                    }

                    $rekap_nilai = $proposal + $hasil + $average / $pembagi;
                    ?>
                    <tr>
                        <td colspan="3"><br><b>Rekapitulasi Nilai</b></td>
                    <tr>
                        <td>Nilai Sem. Proposal</td>
                        <td colspan="2"><?= $proposal ?></td>
                    </tr>
                    <tr>
                        <td>Nilai Sem. Hasil</td>
                        <td colspan="2"><?= $hasil ?></td>
                    </tr>
                    <tr>
                        <td>Nilai Skripsi</td>
                        <td colspan="2"><?= $average ?></td>
                    </tr>
                    <tr>
                        <td><b>TOTAL NILAI</b></td>
                        <td><b><?= round($rekap_nilai,2) ?></b></td>
                        <td>Grade: &nbsp; <b style="font-size: 16px"><?= Sia::grade($mhs->id_prodi, $rekap_nilai) ?></b></td>
                    </tr>
                <?php } ?>

            </table>
        </div>
        <?php
    }

    private function hitungNilai($id_mhs_reg, $jenis)
    {
        $ketua = Sia::penguji($id_mhs_reg, $jenis, 'KETUA', Session::get('ua_semester'));
        $sekretaris = Sia::penguji($id_mhs_reg, $jenis, 'SEKRETARIS', Session::get('ua_semester'));
        $anggota = Sia::penguji($id_mhs_reg, $jenis, 'ANGGOTA', Session::get('ua_semester'));
        $anggota2 = Sia::penguji($id_mhs_reg, $jenis, 'ANGGOTA2', Session::get('ua_semester'));

        $ketua_nil = !empty($ketua->nilai) ? $ketua->nilai : 0;
        $sekretaris_nil = !empty($sekretaris->nilai) ? $sekretaris->nilai : 0;
        $anggota_nil = !empty($anggota->nilai) ? $anggota->nilai : 0;
        $anggota2_nil = !empty($anggota2->nilai) ? $anggota2->nilai : 0;

        $pembagi = 0;

        if ( !empty($ketua->nilai) ) {
            $pembagi += 1;
        }

        if ( !empty($sekretaris->nilai) ) {
            $pembagi += 1;
        }

        if ( !empty($anggota->nilai) ) {
            $pembagi += 1;
        }

        if ( !empty($anggota2->nilai) ) {
            $pembagi += 1;
        }

        if ( !empty($pembagi) ) {
            $average = number_format(($ketua_nil + $sekretaris_nil + $anggota_nil + $anggota2_nil) / $pembagi,2);
        } else {
            $average = 0;
        }

        return $average;
    }

    public function grade(Request $r)
    {
        echo ' : '.Sia::grade($r->prodi,$r->nilai);
    }

    public function nilaiStore(Request $r)
    {
        $id_smt = Session::get('ua_semester');

        try {

            DB::beginTransaction();

            if ( $r->skripsi ) {    

                $this->nilaiStoreSkripsi($r);
            
            } else {

                $pembagi = 0;
                if ( !empty($r->nil_ketua) ) {
                    $pembagi += 1;
                }
                if ( !empty($r->nil_sekretaris) ) {
                    $pembagi += 1;
                }
                if ( !empty($r->nil_anggota) ) {
                    $pembagi += 1;
                }
                if ( !empty($r->nil_anggota2) ) {
                    $pembagi += 1;
                }

                $tot_nilai = $r->nil_ketua + $r->nil_sekretaris + $r->nil_anggota + $r->nil_anggota2;

                if ( empty($tot_nilai) ) {
                    $nilai_akhir = 0;
                } else {
                    $nilai_akhir = number_format($tot_nilai / $pembagi,2);
                }
                
                $nilai_huruf = Sia::grade(Session::get('ua_prodi'), $nilai_akhir);


                $skala_nil = DB::table('skala_nilai')
                    ->where('nilai_huruf', $nilai_huruf)
                    ->where('id_prodi', Session::get('ua_prodi'))
                    ->first();

                if ( empty($skala_nil) ) {
                    return Response::json(['error' => 1,'msg'=> 'Nilai '.$nilai_huruf.' untuk prodi ini tidak ditemukan di skala nilai. Mohon lengkapi skala nilai di modul master']);
                }


                $nilai = number_format(($r->nil_ketua + $r->nil_sekretaris + $r->nil_anggota + $r->nil_anggota2) / 3,2);
                $penguji = [
                    'KETUA' => $r->nil_ketua, 
                    'SEKRETARIS' => $r->nil_sekretaris, 
                    'ANGGOTA' => $r->nil_anggota, 
                    'ANGGOTA2' => $r->nil_anggota2
                ];
                
                foreach( $penguji as $key => $val ) {
                    DB::table('penguji')
                        ->where('id_mhs_reg', $r->id_mhs_reg)
                        ->where('jabatan', $key)
                        ->where('jenis', Session::get('ua_jenis'))
                        ->where('id_smt', $id_smt)
                        ->update(['nilai' => $val]);
                        // $tes[] = $key;
                }


                // Update nilai
                // Cek apakah ada jadwal untuk proposal/hasil
                $cek_krs = DB::table('jadwal_kuliah as jdk')
                            ->leftJoin('matakuliah as mk', 'jdk.id_mk', 'mk.id')
                            ->where('mk.ujian_akhir', Session::get('ua_jenis'))
                            ->where('jdk.id', $r->id_jdk)
                            ->count();

                if ( $cek_krs > 0 ) {

                    DB::table('nilai')
                        ->where('id_jdk', $r->id_jdk)
                        ->where('id_mhs_reg', $r->id_mhs_reg)
                        ->update(['nilai_huruf' => $nilai_huruf, 'nilai_indeks' => $skala_nil->nilai_indeks]);

                } else {

                    if ( Session::get('ua_jenis') == 'P' ) {

                        // Jika proposal simpan nilai pada field nilai_mid
                        DB::table('nilai')
                        ->where('id_jdk', $r->id_jdk)
                        ->where('id_mhs_reg', $r->id_mhs_reg)
                        ->update(['nil_mid' => $nilai_akhir]);
                    
                    } elseif ( Session::get('ua_jenis') == 'H' ) {

                        // Jika hasil simpan nilai pada field nilai_final
                        DB::table('nilai')
                            ->where('id_jdk', $r->id_jdk)
                            ->where('id_mhs_reg', $r->id_mhs_reg)
                            ->update(['nil_final' => $nilai_akhir]);
                    }
                }


            }
            
            Rmt::success('Berhasil menyimpan data');

            DB::commit();

        } catch(\Exception $e) {

            DB::rollback();
            return Response::json([$e->getMessage()], 422);
        
        }

    }

    private function nilaiStoreSkripsi($r)
    {
        try {

            $pembagi = 0;
            $pembagi2 = 1;

            if ( !empty($r->nil_ketua) ) {
                $pembagi += 1;
            }
            if ( !empty($r->nil_sekretaris) ) {
                $pembagi += 1;
            }
            if ( !empty($r->nil_anggota) ) {
                $pembagi += 1;
            }
            if ( !empty($r->nil_anggota2) ) {
                $pembagi += 1;
            }

            $tot_nilai = $r->nil_ketua + $r->nil_sekretaris + $r->nil_anggota + $r->nil_anggota2;

            if ( empty($tot_nilai) ) {
                $nilai_skripsi = 0;
            } else {
                $nilai_skripsi = number_format($tot_nilai / $pembagi,2);
            }

            $nilai_proposal = $this->hitungNilai($r->id_mhs_reg, 'P');
            $nilai_hasil = $this->hitungNilai($r->id_mhs_reg, 'H');

            if ( !empty($proposal) ) {
                $pembagi2 += 1;
            }
            if ( !empty($hasil) ) {
                $pembagi2 += 1;
            }

            $total = $nilai_skripsi + $nilai_proposal + $nilai_hasil / $pembagi2;

            $nilai_huruf = Sia::grade(Session::get('ua_prodi'), $total);

            $skala_nil = DB::table('skala_nilai')
                ->where('nilai_huruf', $nilai_huruf)
                ->where('id_prodi', Session::get('ua_prodi'))
                ->first();

            if ( empty($skala_nil) ) {
                return Response::json(['error' => 1,'msg'=> 'Nilai '.$nilai_huruf.' untuk prodi ini tidak ditemukan di skala nilai. Mohon lengkapi skala nilai di modul master']);
            }

            // if ( Session::get('ua_prodi') <> 61101 ) {
                
            //     $penguji = [
            //         'KETUA' => $r->nil_ketua,
            //         'SEKRETARIS' => $r->nil_sekretaris,
            //         'ANGGOTA' => $r->nil_anggota
            //     ];
            //     $nilai = number_format(($r->nil_ketua + $r->nil_sekretaris + $r->nil_anggota) / 3,2);

            // } else {

                $penguji = [
                    'KETUA' => $r->nil_ketua, 
                    'SEKRETARIS' => $r->nil_sekretaris, 
                    'ANGGOTA' => $r->nil_anggota, 
                    'ANGGOTA2' => $r->nil_anggota2
                ];
            // }

            foreach( $penguji as $key => $val ) {
                DB::table('penguji')
                    ->where('id_mhs_reg', $r->id_mhs_reg)
                    ->where('jabatan', $key)
                    ->where('jenis', Session::get('ua_jenis'))
                    ->where('id_smt', Session::get('ua_semester'))
                    ->update(['nilai' => $val]);
            }

            // Update nilai
            DB::table('nilai')
                ->where('id_jdk', $r->id_jdk)
                ->where('id_mhs_reg', $r->id_mhs_reg)
                ->update(['nilai_huruf' => $nilai_huruf, 'nilai_indeks' => $skala_nil->nilai_indeks]);


        } catch( \Exception $e ) {
            abort(422, $e->getMessage());
        }

    }

    public function beritaAcara(Request $r)
    {
        $jenis = Session::get('ua_jenis');
        $semester = Session::get('ua_semester');

        $data['mhs'] = Sia::mahasiswa()
                ->leftJoin('konsentrasi as k', 'm2.id_konsentrasi', 'k.id_konsentrasi')
                ->select('m2.nim','m1.nm_mhs','m2.id_prodi','p.nm_prodi','p.jenjang',
                        'm2.bebas_pembayaran','m2.jurnal_file','m2.jurnal_approved','m2.semester_mulai',
                        'k.nm_konsentrasi as konsentrasi')
                ->where('m2.id', $r->id_mhs_reg)
                ->first();

        $data['skripsi'] = DB::table('ujian_akhir')
                            ->where('id_mhs_reg', $r->id_mhs_reg)
                            ->where('jenis', $jenis)
                            ->where('id_smt', $semester)
                            ->first();

        // VALIDASI
            $canAksi = [];

            if ( !empty($data['skripsi']->tgl_ujian) && $data['skripsi']->tgl_ujian > '2021-09-15' ) {

                if ( $data['skripsi']->siap_seminar == 0 ) {
                    // Field siap_seminar diupdate menjadi 1 apabila prodi menginput ruangan
                    echo "<center>Ruangan/tempat belum diinput</center>";
                }

                // Cek validasi keuangan dan NDC
                $validasi_lanjutan = $this->persetujuanSeminar($r->id_mhs_reg, $semester, $jenis, $data['mhs']->nim);

                if ( count($validasi_lanjutan) > 0 ) {
                    echo '<center><h3>Data Ujian Mahasiswa ini belum bisa dicetak karena: <br><b>'.implode(', ', $validasi_lanjutan).'</b></h3></center>';
                    exit;
                }
            }

            if ( $data['mhs']->bebas_pembayaran == '0' ) {
                $canAksi[] = 'Pembayaran mahasiswa belum lunas';
            }

            if ( $data['mhs']->id_prodi == 61101 && !empty($r->cetak) && $jenis == 'S' && $data['mhs']->semester_mulai > 20171 ) {
                if ( count($canAksi) > 0 ) {
                    echo '<center><h3>Data Ujian Mahasiswa ini belum bisa dicetak karena: <br><b>'.implode(', ', $canAksi).'</b></h3></center>';
                    exit;
                }
            }


        $data['ketua'] = Sia::penguji($r->id_mhs_reg, $r->jenis, 'KETUA', Session::get('ua_semester'));
        $data['sekretaris'] = Sia::penguji($r->id_mhs_reg, $r->jenis, 'SEKRETARIS', Session::get('ua_semester'));
        $data['penguji'] = DB::table('penguji as p')
                        ->leftJoin('dosen as d', 'p.id_dosen', 'd.id')
                        ->select(DB::raw('concat_ws(\' \',d.gelar_depan,d.nm_dosen,d.gelar_belakang) as penguji'),'p.id_smt', 'p.id_dosen', 'p.jabatan','p.id','p.nilai','d.ttd')
                        ->where('p.id_mhs_reg', $r->id_mhs_reg)
                        ->where('p.jenis', $r->jenis)
                        ->where('p.id_smt', $semester)
                        ->orderBy('p.id')
                        ->take(4)
                        ->get();

        if ( empty($data['penguji']->count()) ) {
            $data['penguji'] = DB::table('penguji as p')
                        ->leftJoin('dosen as d', 'p.id_dosen', 'd.id')
                        ->select(DB::raw('concat_ws(\' \',d.gelar_depan,d.nm_dosen,d.gelar_belakang) as penguji'),'p.id_smt', 'p.id_dosen', 'p.jabatan','p.nilai','d.ttd')
                        ->where('p.id_mhs_reg', $r->id_mhs_reg)
                        ->where('p.jenis', $r->jenis)
                        ->orderBy('p.id')
                        ->take(4)
                        ->get();
        }


        $jenjang = empty($r->jenjang) ? 's2': $r->jenjang;

        if ( empty($data['skripsi']) ) {
            dd('Waktu seminar belum diinput');
        }
        
        switch ($r->cetak) {
            case 'undangan':
                return view('ujian-akhir.'.$jenjang.'.undangan-seminar', $data);
            break;
            case 'daftar-hadir-penguji':
                return view('ujian-akhir.'.$jenjang.'.print-daftar-hadir-penguji', $data);
            break;
            case 'daftar-hadir-ujian':
                return view('ujian-akhir.'.$jenjang.'.print-daftar-hadir-ujian', $data);
            break;
            case 'nilai-ujian':
                return view('ujian-akhir.'.$jenjang.'.print-nilai-ujian', $data);
            break;
            case 'saran':
                return view('ujian-akhir.'.$jenjang.'.print-saran-perbaikan', $data);
            break;
            case 'berita-acara':                
                return view('ujian-akhir.'.$jenjang.'.print-berita-acara', $data);
            break;
            case 'rekapitulasi':
                return view('ujian-akhir.'.$jenjang.'.print-rekapitulasi-nilai', $data);
            break;
            
            default:
                // S1
                if ( $jenis == 'S' ) {
                    if ( $r->cetak == 'penilaian' ) {
                        return view('ujian-akhir.s1.penilaian', $data);
                    } else {
                        return view('ujian-akhir.print-berita-acara-skripsi', $data);
                    }
                } else {
                    return view('ujian-akhir.s1.penilaian', $data);
                }
            break;
        }
    }

    private function persetujuanSeminar($id_mhs_reg, $id_smt, $jenis, $nim)
    {
        // Ambil yang telah disetujui BAUK dan NDC (Jika seminar hasil)
        $val_1 = DB::table('seminar_pendaftaran')
                ->where('id_mhs_reg', $id_mhs_reg)
                ->where('id_smt', $id_smt)
                ->where('jenis', $jenis)
                ->first();
        
        $angkatan = substr($nim, 0, 4);

        $msg = [];

        if ( !empty($val_1) ) {

            if ( $jenis == 'H' && $val_1->validasi_ndc == 0 ) {

                // Hanya periksa angkatan 2018 ke atas
                if ( $angkatan > 2017 ) {
                    $msg[] = 'Belum valid dari NDC';
                }
            }

            if ( $val_1->validasi_bauk == 0 ) {
                $msg[] = 'Belum valid dari keuangan';
            }
        }
        
        return $msg;
    }

    public function rekapNilai(Request $r)
    {

        $mhs = DB::table('nilai as n')
                ->join('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
                ->join('ujian_akhir as ua', 'ua.id_mhs_reg', 'n.id_mhs_reg')
                ->select('ua.tgl_ujian')
                ->where('ua.jenis', Session::get('ua_jenis'))
                ->where('ua.id_smt', Session::get('ua_semester'))
                ->where('ua.tgl_ujian', '<>', '0000-00-00')
                ->where('jdk.id_prodi', Session::get('ua_prodi'))
                ->where('jdk.id_smt', Session::get('ua_semester'))
                ->orderBy('ua.tgl_ujian', 'desc')
                ->groupBy('ua.tgl_ujian')
                ->get(); 
        ?>

        <div class="table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover" data-provide="data-table">
                <thead class="custom">
                    <tr>
                        <th>No.</th>
                        <th align="left">Tanggal Ujian</th>
                    </tr>
                </thead>
                <tbody align="center">
                    <?php $no = 1 ?>
                    <?php foreach( $mhs as $m ) { ?>
                        <tr style="cursor: pointer;" onclick="goCetak('<?= $m->tgl_ujian ?>')">
                            <td><?= $no++ ?></td>
                            <td align="left"><?= Rmt::tgl_indo($m->tgl_ujian) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <!-- Library datable -->
        <script type="text/javascript" src="<?= url('resources') ?>/assets/plugins/datable/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="<?= url('resources') ?>/assets/plugins/datable/dataTables.bootstrap.js"></script>
        <script>
            $(function(){
                $('table[data-provide="data-table"]').dataTable();
            });
        </script>
        <?php
    }

    public function cetakRekapNilai(Request $r)
    {
        $data['prodi'] = DB::table('prodi')
                        ->where('id_prodi', Session::get('ua_prodi'))
                        ->first();

        $data['mhs'] = DB::table('ujian_akhir as ua')
                ->leftJoin('mahasiswa_reg as m1', 'ua.id_mhs_reg', 'm1.id')
                ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                ->select('m1.nim','m2.nm_mhs',
                    DB::raw('(SELECT group_concat(nilai) from penguji
                        where id_mhs_reg=m1.id
                        and jenis=\''.Session::get('ua_jenis').'\') as nilai'),
                    DB::raw('(SELECT sum(nilai) from penguji
                        where id_mhs_reg=m1.id
                        and jenis=\''.Session::get('ua_jenis').'\') as total_nilai'))
                ->where('ua.tgl_ujian', $r->tgl)
                ->where('m1.id_prodi', Session::get('ua_prodi'))
                ->where('ua.jenis', Session::get('ua_jenis'))
                ->orderBy('m1.nim')->get();

        return view('ujian-akhir.print-rekap-nilai', $data);
    }

    public function cetakPernyataan(Request $r)
    {
        $data['prodi'] = DB::table('prodi')
                        ->where('id_prodi', Session::get('ua_prodi'))
                        ->first();

        $data['mhs'] = Sia::mahasiswa()
                ->leftJoin('konsentrasi as k', 'm2.id_konsentrasi', 'k.id_konsentrasi')
                ->select('m2.nim','m1.nm_mhs','m2.id_prodi','p.nm_prodi','p.jenjang',
                        'm1.alamat','m1.hp',
                        'k.nm_konsentrasi as konsentrasi')
                ->where('m2.id', $r->id_mhs_reg)
                ->first();

        $data['skripsi'] = DB::table('ujian_akhir')
                            ->where('id_mhs_reg', $r->id_mhs_reg)
                            ->where('jenis', $r->jenis)
                            ->first();

        return view('ujian-akhir.s2.print-surat-pernyataan-tesis', $data);
    }

    public function cetakJadwalSeminar(Request $r)
    {
        $data['jenis'] = Session::get('ua_jenis');
        $mhs = Session::get('mhs_in_ujian_akhir');
        $mhsArr = [];

        foreach( $mhs as $val ) {
            $mhsArr[] = $val->id_mhs_reg;
        }

        $data['mahasiswa'] = DB::table('ujian_akhir as ua')
                ->leftJoin('mahasiswa_reg as m1', 'ua.id_mhs_reg', 'm1.id')
                ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                ->select('m1.nim', 'm2.nm_mhs','ua.*')
                ->where('ua.jenis', $data['jenis'])
                ->whereNotNull('ua.pukul')
                ->whereIn('ua.id_mhs_reg', $mhsArr)
                ->where('ua.tgl_ujian', $r->tgl_ujian)
                ->where('ua.ruangan', $r->ruang)
                ->orderBy('ua.pukul')
                ->get();

        return view('ujian-akhir.s2.jadwal-seminar', $data);
    }

    public function eksporTelahUjian(Request $r)
    {
        
        $id_smt = Session::get('ua_semester');
        $jenis = Session::get('ua_jenis');
        $id_prodi = Session::get('ua_prodi');

        $query = DB::table('ujian_akhir as ua')
                    ->join('mahasiswa_reg as m1', 'ua.id_mhs_reg', 'm1.id')
                    ->join('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->join('prodi as pr', 'm1.id_prodi', 'pr.id_prodi')
                    ->select('m1.nim','m2.nm_mhs','pr.nm_prodi','pr.jenjang','pr.id_prodi',
                        DB::raw('(select group_concat(distinct d.gelar_depan," ", d.nm_dosen,", ", d.gelar_belakang SEPARATOR \'<br>\') as d from penguji as p 
                                left join dosen as d on p.id_dosen = d.id
                                where p.id_mhs_reg=m1.id
                                AND p.jabatan=\'KETUA\'
                                AND p.jenis=\''.$jenis.'\') as pembimbing'))
                    ->where('ua.id_smt', $id_smt)
                    ->whereNotNull('ruangan')
                    ->where('m1.id_prodi', $id_prodi);

        $data_mhs = $query->orderBy('m1.nim')->get();

        $data['mahasiswa'] = $data_mhs;

        $data['jenis'] = $jenis;

        // return view('ujian-akhir.ekspor-telah-ujian', $data);

        try {
            Excel::create('Mahasiswa Telah Ujian', function($excel)use($data) {

                $excel->sheet('New sheet', function($sheet)use($data) {

                    $sheet->loadView('ujian-akhir.ekspor-telah-ujian', $data);

                });

            })->download('xlsx');
        } catch(\Exception $e) {
            echo $e->getMessage();
        }

        
    }
}
