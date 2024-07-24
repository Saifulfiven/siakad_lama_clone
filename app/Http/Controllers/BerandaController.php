<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sia, DB, Rmt, Session, Auth, Carbon, Response;
use App\User;

class BerandaController extends Controller
{
    use KeadaanMahasiswa;
    use KeadaanDosen;

    public function __construct()
    {
       $this->middleware('auth');
    }

    public function index()
    {
        $data[] = '';
        
        switch( Auth::user()->level ) {
            case 'admin':
            case 'akademik':
            case 'keuangan':
            case 'ketua':
            case 'ketua 1':
            case 'personalia':
            case 'cs':

                $akm = "SELECT id_mhs_reg from aktivitas_kuliah
                        where id_smt='".Sia::sessionPeriode()."'";
                // For aktivitas

                $data['prodi'] = DB::table('prodi as pr')
                        ->select('pr.id_prodi','pr.jenjang','pr.nm_prodi',
                            DB::raw('(SELECT COUNT(*) AS agr FROM aktivitas_kuliah as akm
                                left join mahasiswa_reg as m1 on akm.id_mhs_reg=m1.id
                                where akm.id_smt='.Sia::sessionPeriode().'
                                and m1.id_prodi = pr.id_prodi) as jml'),
                            DB::raw('(SELECT count(*) from mahasiswa_reg
                                    where id not in ('.$akm.')
                                    and semester_mulai <= '.Sia::sessionPeriode().'
                                    and id_jenis_keluar = 0 
                                    and id_prodi=pr.id_prodi) as lainnya'))
                        ->whereIn('pr.id_prodi', Sia::getProdiUser())
                        ->orderBy('jenjang')->get();

                $data['prodi2'] = DB::table('prodi as pr')
                            ->select('pr.id_prodi','pr.jenjang','pr.nm_prodi',
                                DB::raw('(SELECT COUNT(*) as agr from mahasiswa_reg
                                    where id_prodi=pr.id_prodi
                                    and id_jenis_keluar <> 0
                                    and semester_keluar = '.Sia::sessionPeriode().') as jml'))
                            ->whereIn('pr.id_prodi', Sia::getProdiUser())
                            ->orderBy('jenjang')->get();

                $data['jml_maba'] = DB::table('prodi as pr')
                            ->select('pr.id_prodi','pr.jenjang','pr.nm_prodi',
                                DB::raw('(SELECT COUNT(*) AS agr FROM mahasiswa_reg
                                    where semester_mulai='.Sia::sessionPeriode().'
                                    and id_prodi = pr.id_prodi) as jml'))
                            ->whereIn('pr.id_prodi', Sia::getProdiUser())
                            ->orderBy('jenjang')->get();

                $data['ipk'] = DB::select("
                        SELECT nm_prodi,jenjang, 
                            (SELECT round(sum(akm.ipk)/count(akm.ipk),2) from aktivitas_kuliah as akm
                                left join mahasiswa_reg as m on akm.id_mhs_reg = m.id
                                where akm.id_smt=".Sia::sessionPeriode()."
                                and m.id_prodi=prodi.id_prodi
                                and akm.status_mhs='A'
                                and akm.ipk > 0) as ipk
                        from prodi
                        where id_prodi in (".implode(',',Sia::getProdiUser()).")
                        order by jenjang
                    ");

            break;

            case 'pengawas':
                return redirect(route('ku_status'));
            break;

        }

        // dd($data);
    
        return view('beranda.index', $data);
    }

    public function detailAkm(Request $r)
    {
        if ( $r->status == 'x' ) {
        // tak diketahui
            $this->detailAkmLainnya($r);
            exit;
        }

        $listAngkatan = DB::select("
            SELECT angkatan from (
                SELECT left(nim,4) as angkatan
                    from mahasiswa_reg
                    where id_jenis_keluar = 0
                    and semester_mulai <= ".Sia::sessionPeriode()."
                    and id_prodi = ".$r->id_prodi."
                    group by semester_mulai
            ) as r1 group by angkatan order by angkatan
            "); ?>

        <table class="table table-bordered table-striped">
            <tr>
                <th colspan="4">Program Studi <?= $r->prodi ?></th>
            </tr>
            <tr>
                <th>Angkatan</th>
                <th>Laki-laki</th>
                <th>Perempuan</th>
                <th>Jumlah</th>
            </tr>
            <tbody align="center">
            <?php

            $total_laki = 0;
            $total_perempuan = 0;
            $total = 0;

            foreach( $listAngkatan as $ang ) {
                $perempuan = DB::table('aktivitas_kuliah as ak')
                        ->leftJoin('mahasiswa_reg as m', 'ak.id_mhs_reg', 'm.id')
                        ->leftJoin('mahasiswa as m2', 'm2.id', 'm.id_mhs')
                        ->where('ak.status_mhs', $r->status)
                        ->where('ak.id_smt', Sia::sessionPeriode())
                        ->whereRaw('left(nim,4)='.$ang->angkatan)
                        ->where('m.id_prodi', $r->id_prodi)
                        ->where('m2.jenkel', 'P')->count();
                $laki = DB::table('aktivitas_kuliah as ak')
                        ->leftJoin('mahasiswa_reg as m', 'ak.id_mhs_reg', 'm.id')
                        ->leftJoin('mahasiswa as m2', 'm2.id', 'm.id_mhs')
                        ->where('ak.status_mhs', $r->status)
                        ->where('ak.id_smt', Sia::sessionPeriode())
                        ->whereRaw('left(nim,4)='.$ang->angkatan)
                        ->where('m.id_prodi', $r->id_prodi)
                        ->where('m2.jenkel', 'L')->count();
                
                $jml = $laki + $perempuan;
                if ( $jml == 0 ) continue;

                $total_perempuan += $perempuan;
                $total_laki += $laki;
                $total += $jml;

                ?>
                <tr>
                    <td><?= $ang->angkatan ?></td>
                    <td><?= $laki ?></td>
                    <td><?= $perempuan ?></td>
                    <td><?= $jml ?></td>
                </tr>

            <?php } ?>
            <tr>
                <td><strong>TOTAL</strong></td>
                <td><strong><?= $total_laki ?></strong></td>
                <td><strong><?= $total_perempuan ?></strong></td>
                <td><strong><?= $total ?></strong></td>
            </tr>
        </tbody>
        </table>
    <?php
    }

    private function detailAkmLainnya($r)
    {
        $akm = "SELECT id_mhs_reg from aktivitas_kuliah
                where id_smt=".Sia::sessionPeriode();

        $akm2 = DB::table('aktivitas_kuliah')
                    ->where('id_smt', Sia::sessionPeriode())
                    ->pluck('id_mhs_reg');

        $listAngkatan = DB::select("
            SELECT angkatan from (
                SELECT left(nim,4) as angkatan
                    from mahasiswa_reg
                    where id_jenis_keluar = 0
                    and semester_mulai <= ".Sia::sessionPeriode()."
                    and id_prodi = ".$r->id_prodi."
                    and id not in (".$akm.")
                    group by semester_mulai
            ) as r1 group by angkatan order by angkatan
            ");

            ?>

        <table class="table table-bordered table-striped">
            <tr>
                <th colspan="4">Program Studi <?= $r->prodi ?></th>
            </tr>
            <tr>
                <th>Angkatan</th>
                <th>Laki-laki</th>
                <th>Perempuan</th>
                <th>Jumlah</th>
            </tr>
            <tbody align="center">
            <?php

            $total_laki = 0;
            $total_perempuan = 0;
            $total = 0;

            foreach( $listAngkatan as $ang ) {

                $perempuan = DB::select("
                        SELECT m1.nim from mahasiswa_reg m1
                            left join mahasiswa as m2 on m1.id_mhs = m2.id
                            where m1.id_jenis_keluar = 0
                            and m1.semester_mulai <= ".Sia::sessionPeriode()."
                            and m1.id not in (".$akm.")
                            and m1.id_prodi = ".$r->id_prodi."
                            and left(m1.nim,4) = ".$ang->angkatan."
                            and m2.jenkel = 'P'");

                $laki = DB::select("
                        SELECT m1.nim from mahasiswa_reg m1
                            left join mahasiswa as m2 on m1.id_mhs = m2.id
                            where m1.id_jenis_keluar = 0
                            and m1.semester_mulai <= ".Sia::sessionPeriode()."
                            and m1.id not in (".$akm.")
                            and m1.id_prodi = ".$r->id_prodi."
                            and left(m1.nim,4) = ".$ang->angkatan."
                            and m2.jenkel = 'L'");

                $data = array_merge($perempuan,$laki);

                $jml = count($laki) + count($perempuan);
                if ( $jml == 0 ) continue;

                $total_perempuan += count($perempuan);
                $total_laki += count($laki);
                $total += $jml;

                ?>
                <tr>
                    <td><?= $ang->angkatan ?></td>
                    <td><?= count($laki) ?></td>
                    <td><?= count($perempuan) ?></td>
                    <td><?= $jml ?></td>
                </tr>

            <?php } ?>
            <tr>
                <td><strong>TOTAL</strong></td>
                <td><strong><?= $total_laki ?></strong></td>
                <td><strong><?= $total_perempuan ?></strong></td>
                <td><strong><?= $total ?></strong></td>
            </tr>
            </tbody>
        </table>

        <?php if ( count($data) > 0 ) { ?>
            <a href="javascript:;" id="tampilkan">Tampilkan mahasiswa</a>
            <div style="display: none;padding-top: 10px" id="data">
                <!-- <hr> -->
                <table class="table table-bordered" style="max-width: 150px !important">
                    <tr>
                        <th width="20">No</th>
                        <th>NIM</th>
                    </tr>
                    <?php $no = 1 ?>
                    <?php foreach( $data as $d ) { ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $d->nim ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
            <script>
                $(function(){
                    $('#tampilkan').click(function(){
                        $('#data').slideDown();
                    });
                });
            </script>
        <?php } ?>
    <?php
    }

    public function detailAkmTotal(Request $r)
    {
        $akm = "SELECT id_mhs_reg from aktivitas_kuliah
                        where id_smt='".Sia::sessionPeriode()."'";

        $listAngkatan = DB::select("
            SELECT angkatan from (
                SELECT left(nim,4) as angkatan
                    from mahasiswa_reg
                    where id_jenis_keluar = 0
                    and semester_mulai <= ".Sia::sessionPeriode()."
                    and id_prodi = ".$r->id_prodi."
                    group by semester_mulai
            ) as r1 group by angkatan order by angkatan
            "); ?>

        <table class="table table-bordered table-striped">
            <tr>
                <th colspan="4">Program Studi <?= $r->prodi ?></th>
            </tr>
            <tr>
                <th>Angkatan</th>
                <th>Laki-laki</th>
                <th>Perempuan</th>
                <th>Jumlah</th>
            </tr>
            <tbody align="center">
            <?php

            $total_laki = 0;
            $total_perempuan = 0;
            $total = 0;

            foreach( $listAngkatan as $ang ) {

                $perempuan_lainnya = DB::select("
                        SELECT COUNT(*) AS jml from mahasiswa_reg m1
                            left join mahasiswa as m2 on m1.id_mhs = m2.id
                            where m1.id_jenis_keluar = 0
                            and m1.semester_mulai <= ".Sia::sessionPeriode()."
                            and m1.id not in (".$akm.")
                            and m1.id_prodi = ".$r->id_prodi."
                            and left(m1.nim,4) = ".$ang->angkatan."
                            and m2.jenkel = 'P'");

                $perempuan = DB::table('aktivitas_kuliah as ak')
                        ->leftJoin('mahasiswa_reg as m', 'ak.id_mhs_reg', 'm.id')
                        ->leftJoin('mahasiswa as m2', 'm2.id', 'm.id_mhs')
                        ->where('ak.id_smt', Sia::sessionPeriode())
                        ->whereRaw('left(nim,4)='.$ang->angkatan)
                        ->where('m.id_prodi', $r->id_prodi)
                        ->where('m2.jenkel', 'P')->count();

                $laki_lainnya = DB::select("
                        SELECT COUNT(*) AS jml from mahasiswa_reg m1
                            left join mahasiswa as m2 on m1.id_mhs = m2.id
                            where m1.id_jenis_keluar = 0
                            and m1.semester_mulai <= ".Sia::sessionPeriode()."
                            and m1.id not in (".$akm.")
                            and m1.id_prodi = ".$r->id_prodi."
                            and left(m1.nim,4) = ".$ang->angkatan."
                            and m2.jenkel = 'L'");

                $laki = DB::table('aktivitas_kuliah as ak')
                        ->leftJoin('mahasiswa_reg as m', 'ak.id_mhs_reg', 'm.id')
                        ->leftJoin('mahasiswa as m2', 'm2.id', 'm.id_mhs')
                        ->where('ak.id_smt', Sia::sessionPeriode())
                        ->whereRaw('left(nim,4)='.$ang->angkatan)
                        ->where('m.id_prodi', $r->id_prodi)
                        ->where('m2.jenkel', 'L')->count();

                $laki_laki = $laki + $laki_lainnya[0]->jml;
                $wanita = $perempuan + $perempuan_lainnya[0]->jml;
                $jml = $laki_laki + $wanita;

                if ( $jml == 0 ) continue;

                $total_perempuan += $wanita;
                $total_laki += $laki_laki;
                $total += $jml;

                ?>
                <tr>
                    <td><?= $ang->angkatan ?></td>
                    <td><?= $laki_laki ?></td>
                    <td><?= $wanita ?></td>
                    <td><?= $jml ?></td>
                </tr>

            <?php } ?>
            <tr>
                <td><strong>TOTAL</strong></td>
                <td><strong><?= $total_laki ?></strong></td>
                <td><strong><?= $total_perempuan ?></strong></td>
                <td><strong><?= $total ?></strong></td>
            </tr>
        </tbody>
        </table>
    <?php
    }

/*
    public function detailIpkAktif(Request $r)
    {
        $ipk = DB::select("
                    SELECT round(sum(akm.ipk)/count(akm.ipk),2) as ipk
                        from aktivitas_kuliah as akm
                        left join mahasiswa_reg as m on akm.id_mhs_reg = m.id
                        where akm.id_smt=".Sia::sessionPeriode()."
                        and m.id_prodi=prodi.id_prodi
                        and akm.status_mhs='A'
                        and akm.ipk > 0) as ipk"); ?>

        <table class="table table-bordered table-striped">
            <tr>
                <th colspan="4">Program Studi <?= $r->prodi ?></th>
            </tr>
            <tr>
                <th>IPK Min</th>
                <th>Rata2 IPK Aktif</th>
                <th>IPK Max</th>
            </tr>
            <tbody align="center">
                <tr>
                    <td>
                </tr>
            </tbody>
        </table>
        <?php

    }
*/

    public function keadaanMhs(Request $r)
    { 

        $filter_prodi = '';
        $prodi_aktif = '';
        $tot_maba_l = 0;
        $tot_maba_p = 0;
        $tot_maba = 0;
        
        $tot_aktif_l = 0;
        $tot_aktif_p = 0;
        $tot_aktif = 0;

        $tot_non_aktif_l = 0;
        $tot_non_aktif_p = 0;
        $tot_non_aktif = 0;

        $tot_lulus_l = 0;
        $tot_lulus_p = 0;
        $tot_lulus = 0;

        $tot_do_l = 0;
        $tot_do_p = 0;
        $tot_do = 0;

        $tot_lain_l = 0;
        $tot_lain_p = 0;
        $tot_lain = 0;

        if ( $r->prodi ) {
            $prodi_aktif = $r->prodi;
            $filter_prodi = "and id_prodi = $prodi_aktif";
        }

        $listAngkatan = DB::select("
            SELECT angkatan from (
                SELECT left(nim,4) as angkatan
                    from mahasiswa_reg
                    where semester_mulai <= ".Sia::sessionPeriode()."
                    $filter_prodi
                    group by semester_mulai
            ) as r1 group by angkatan order by angkatan
            ");
        ?>
        <div class="table-responsive">

            <table class="table table-bordered table-striped table-hover">
                <thead class="custom">
                    <tr>
                        <th colspan="19" style="text-align: left">
                            Prodi : 
                            <select class="form-custom" onchange="keadaanMahasiswa(this.value)">
                                <option value="">Semua</option>
                                <?php foreach( Sia::listProdi() as $pr ) { ?>
                                    <option value="<?= $pr->id_prodi ?>" <?= $prodi_aktif == $pr->id_prodi ? 'selected':'' ?>><?= $pr->nm_prodi.' ('.$pr->jenjang .')' ?></option>
                                <?php } ?>
                            </select>
                        </th>
                    </tr>
                    <tr>
                        <th rowspan="3">ANGKATAN</th>
                        <th colspan="3">TERDAFTAR</th>
                        <th colspan="3">AKTIF</th>
                        <th colspan="3">NON AKTIF</th>
                        <th colspan="9">ALUMNI</th>
                    </tr>
                    <tr>
                        <th rowspan="2" width="60">L</th>
                        <th rowspan="2" width="60">P</th>
                        <th rowspan="2" width="60">TOT</th>
                        <th rowspan="2" width="60">L</th>
                        <th rowspan="2" width="60">P</th>
                        <th rowspan="2" width="60">TOT</th>
                        <th rowspan="2" width="60">L</th>
                        <th rowspan="2" width="60">P</th>
                        <th rowspan="2" width="60">TOT</th>
                        <th colspan="3" width="60">LULUS</th>
                        <th colspan="3" width="60">DO</th>
                        <th colspan="3" width="60">LAINNYA</th>
                    </tr>
                    <tr>
                        <th rowspan="2" width="60">L</th>
                        <th rowspan="2" width="60">P</th>
                        <th rowspan="2" width="60">TOT</th>
                        <th rowspan="2" width="60">L</th>
                        <th rowspan="2" width="60">P</th>
                        <th rowspan="2" width="60">TOT</th>
                        <th rowspan="2" width="60">L</th>
                        <th rowspan="2" width="60">P</th>
                        <th rowspan="2" width="60">TOT</th>
                    </tr>
                </thead>
                <tbody align="center">
                    <?php foreach( $listAngkatan as $ang ) { 

                        $maba = $this->maba($ang->angkatan, $prodi_aktif);
                        $aktif = $this->aktif($ang->angkatan, 'A', $prodi_aktif);
                        $non_aktif = $this->aktif($ang->angkatan,'N', $prodi_aktif);
                        $lulus = $this->alumni($ang->angkatan,1, $prodi_aktif);
                        $do = $this->alumni($ang->angkatan,3, $prodi_aktif);
                        $lainnya = $this->alumni($ang->angkatan,'x', $prodi_aktif);

                        ?>
                        <tr>
                            <td><?= $ang->angkatan ?></td>
                            <!-- terdaftar -->
                            <td><?= $maba[0] ?></td>
                            <td><?= $maba[1] ?></td>
                            <td style="font-weight: bold"><?= array_sum($maba) ?></td>

                            <!-- aktif -->
                            <td><?= $aktif[0] ?></td>
                            <td><?= $aktif[1] ?></td>
                            <td style="font-weight: bold"><?= array_sum($aktif) ?></td>

                            <!-- non aktif -->
                            <td><?= $non_aktif[0] ?></td>
                            <td><?= $non_aktif[1] ?></td>
                            <td style="font-weight: bold"><?= array_sum($non_aktif) ?></td>

                            <!-- alumni -->
                            <td><?= $lulus[0] ?></td>
                            <td><?= $lulus[1] ?></td>
                            <td style="font-weight: bold"><?= array_sum($lulus) ?></td>

                            <td><?= $do[0] ?></td>
                            <td><?= $do[1] ?></td>
                            <td style="font-weight: bold"><?= array_sum($do) ?></td>

                            <td><?= $lainnya[0] ?></td>
                            <td><?= $lainnya[1] ?></td>
                            <td style="font-weight: bold"><?= array_sum($lainnya) ?></td>
                        </tr>
                        <?php
                            $tot_maba_l += $maba[0];
                            $tot_maba_p += $maba[1];
                            $tot_maba += array_sum($maba);
                            
                            $tot_aktif_l += $aktif[0];
                            $tot_aktif_p += $aktif[1];
                            $tot_aktif += array_sum($aktif);

                            $tot_non_aktif_l += $non_aktif[0];
                            $tot_non_aktif_p += $non_aktif[1];
                            $tot_non_aktif += array_sum($non_aktif);

                            $tot_lulus_l += $lulus[0];
                            $tot_lulus_p += $lulus[1];
                            $tot_lulus += array_sum($lulus);

                            $tot_do_l += $do[0];
                            $tot_do_p += $do[1];
                            $tot_do += array_sum($do);

                            $tot_lain_l += $lainnya[0];
                            $tot_lain_p += $lainnya[1];
                            $tot_lain += array_sum($lainnya);
                        ?>
                    <?php } ?>
                    <tr>
                        <td><strong>TOTAL</strong></td>
                        <td style="font-weight: bold"><?= $tot_maba_l ?></td>
                        <td style="font-weight: bold"><?= $tot_maba_p ?></td>
                        <td style="font-weight: bold"><?= $tot_maba ?></td>

                        <td style="font-weight: bold"><?= $tot_aktif_l ?></td>
                        <td style="font-weight: bold"><?= $tot_aktif_p ?></td>
                        <td style="font-weight: bold"><?= $tot_aktif ?></td>

                        <td style="font-weight: bold"><?= $tot_non_aktif_l ?></td>
                        <td style="font-weight: bold"><?= $tot_non_aktif_p ?></td>
                        <td style="font-weight: bold"><?= $tot_non_aktif ?></td>

                        <td style="font-weight: bold"><?= $tot_lulus_l ?></td>
                        <td style="font-weight: bold"><?= $tot_lulus_p ?></td>
                        <td style="font-weight: bold"><?= $tot_lulus ?></td>

                        <td style="font-weight: bold"><?= $tot_do_l ?></td>
                        <td style="font-weight: bold"><?= $tot_do_p ?></td>
                        <td style="font-weight: bold"><?= $tot_do ?></td>

                        <td style="font-weight: bold"><?= $tot_lain_l ?></td>
                        <td style="font-weight: bold"><?= $tot_lain_p ?></td>
                        <td style="font-weight: bold"><?= $tot_lain ?></td>
                    </tr>
                </tbody>

            </table>
        </div>
        <br><br>
        <center>
            <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger btn-close">Tutup</button>
        </center>
    <?php
    }

    public function keadaanDosen(Request $r)
    {
        $this->keadaanDosenIndex();
    }

    public function ubahPeriode(Request $r)
    {
		$result = DB::table('semester')
						->where('id_smt', $r->smt)->first();
        
		Session::put('periode_aktif', $result->id_smt);
		Session::put('nm_periode_aktif', $result->nm_smt);
		// posisi_periode berisi 1 (ganjil) atau 2 (genap)
		Session::put('posisi_periode', $result->smt);

		return redirect()->back();
    }

    public function ubahNim(Request $r)
    {
        Session::set('nim', $r->nim);

        return redirect()->back();
    }

    public function uuid(Request $r)
    {

    	for( $i = 1; $i <= $r->jml; $i++ )
    	{
    		echo Rmt::uuid().'<br>';
    	}
    }

    public function validasi()
    {
        $data['transfer'] = DB::table('v_konversi_0')->whereIn('id_prodi', Sia::getProdiUser())->orderBy('nim')->get();
        $data['umur'] = DB::table('v_umur_mhs')->where('umur','<',15)->get();
        $data['nilai'] = Sia::nilaiBelumMasuk()->paginate(20);
        return view('beranda.validasi.index', $data);
    }

    public function kalenderAkademik(Request $r)
    {
        // Kategori
        // 1. Magister
        // 2. S1

        // echo "Dalam proses pengembangan";
        // exit;
        $role = 'dosen';

        $kategori = [
            61101 => 1,
            61201 => 2,
            62201 => 2,
            59201 => 2
        ];

        if ( Sia::role('mahasiswa') ) {
            $role = 'mahasiswa';
            $kategori = $kategori[Sia::sessionMhs('prodi')];
        } else {
            $role = 'dosen';
            $kategori = 1;
        }

        $query = DB::table('x_kalender_akademik');
        if ( Sia::role('mahasiswa') ) {
            $query->where('kategori', $kategori);
        }

        $kalender = $query->get();

        foreach( $kalender as $kal ) {

            if ( $role == 'mahasiswa' ) { ?>

                <?php if ( Sia::sessionMhs('prodi') != 61101 ) { ?>
                    <p><a href="<?= $kal->deskripsi ?>" target="_blank" class="btn btn-sm btn-primary">Lihat Kalender Akademik</a></p>
                <?php } else { ?>
                    <p>Data belum tersedia</p>
                <?php } ?>


            <?php } elseif ( $role == 'dosen' ) { ?>

                <p><a href="<?= $kal->deskripsi ?>" target="_blank" class="btn btn-sm btn-primary">Lihat Kalender Akademik <?= $kal->kategori == 2 ? 'S1' : 'S2' ?></a></p>

            <?php }
        }

    }

    public function shutdown()
    {
        touch(storage_path().'/framework/down');
    }

    public function start(Request $r)
    {
        @unlink(storage_path().'/framework/down');
    }

    public function idMhsReg(Request $r)
    {
        if ( strlen($r->nim) > 15 ) {
            $res = \App\Mahasiswareg::where('id', $r->nim)->first();
        } else {
            $res = \App\Mahasiswareg::where('nim', $r->nim)->first();
        }
        
        $data = ['id' => $res->id, 'nim' => $res->nim];

        print_r($data);        
    }

    public function cekMhsForlap()
    {
        return view('beranda.cek-mhs-forlap');
    }


    public function getPerguruanTinggi(Request $r)
    {
        $param = $r->input('query');
        if ( !empty($r->query) ) {
            $wilayah =  DB::table('fdr_all_pt')
                            ->where('kode_perguruan_tinggi', 'like', '%'.$param.'%')
                            ->orWhere('nama_perguruan_tinggi', 'like', '%'.$param.'%')
                            ->orderBy('nama_perguruan_tinggi', 'asc')
                            ->take(10)->get();

        } else {
            $wilayah =  DB::table('fdr_all_pt')
                            ->take(10)->get();
        }
        $data = [];
        foreach( $wilayah as $r ) {
            $data[] = ['data' => $r->id_perguruan_tinggi, 'value' => $r->kode_perguruan_tinggi ." - ". $r->nama_perguruan_tinggi];
        }
        $response = ['query' => 'Unit', 'suggestions' => $data];
        return Response::json($response,200);
    }

    public function getAllProdi(Request $r)
    {
        $data = DB::table('fdr_all_prodi')
                ->where('id_perguruan_tinggi', $r->id_pt)
                ->orderBy('nama_jenjang', 'asc')
                ->orderBy('nama_prodi', 'asc')
                ->get();

        if ( $r->data == 'array' ) {

            $result = $data;

        } else { ?>

            <select name="id_prodi_asal" class="form-control">
                <option value="">Pilih Prodi asal</option>
            
            <?php foreach( $data as $val ) { ?>

                <option value="<?= $val->id_prodi ?>"><?= $val->nama_jenjang ?> - <?= $val->nama_prodi ?></option>

            <?php }

        }
    }

    public function reLogin($id)
    {
        $user = User::find($id);
        Auth::login($user);
        Session::pull('periode_aktif');
        Session::pull('current_admin');
        $from = Session::get('switch_from');
        Session::pull('switch_from');
        if ( $from == 'dosen' ) {
            return redirect(url('/dosen'));
        } elseif ( $from = 'mahasiswa' ) {
            return redirect(url('/mahasiswa'));
        } else {
            return redirect(url('/users'));
        }
    }

    public function setFilter(Request $r)
    {
        if ( !empty($r->modul) ) {

            if ( $r->val == 'all' ) {
                Session::pull($r->key.'.'.$r->modul);
            } else {
                Session::put($r->key.'.'.$r->modul, $r->val);
            }
        }

        if ( $r->remove ) {
            Session::pull($r->key);
        }

        return redirect()->back();
    }

}