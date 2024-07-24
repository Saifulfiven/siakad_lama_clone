<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB, Sia, Rmt, Response, Session, Carbon, Excel;
use App\Pembayaran, App\Mahasiswareg;
use App\Briva, App\BrivaMember;
class KeuanganController extends Controller
{

    /* Pembayaran kuliah */
        public function index(Request $r)
        {

            // Update status bayar
            if ( !empty($r->update_status_bayar) ) {

                if ( $this->updateStatusBayar($r) ) return redirect()->back();
            }

            // Set Filter
                if ( !empty($r->smt) ) {
                    Session::set('mhs_keu_smt', $r->smt);
                }

                if ( !empty($r->angkatan) ) {
                    if ( $r->angkatan == 'all' ) {
                        Session::pull('mhs_keu_smtin');
                    } 
                    Session::set('mhs_keu_angkatan', $r->angkatan);
                }

                if ( !empty($r->smtin) ) {
                    Session::set('mhs_keu_smtin', $r->smtin);
                }

                if ( !empty($r->prodi) ) {
                    Session::set('mhs_keu_prodi', $r->prodi);
                }

                if ( !empty($r->status) || $r->status === '0' ) {
                    Session::set('mhs_keu_status', $r->status);
                }

                if ( !empty($r->bayar) ) {
                    Session::set('mhs_keu_bayar', $r->bayar);
                }

                if ( !Session::has('mhs_keu_status') ) {
                    $this->setSessionFilter();
                }
            // End set

            $mhs = Sia::MhsKeuangan(Session::get('mhs_keu_smt'));
            // Filter angkatan
            if ( Session::get('mhs_keu_angkatan') != 'all' ) {
                $mhs->whereRaw('left('.Sia::prefix().'m2.nim,4)='.Session::get('mhs_keu_angkatan'));
            }

            // Filter smt masuk ganjil/genap
            if ( Session::get('mhs_keu_angkatan') != 'all') {
                $smtin = Session::get('mhs_keu_angkatan').Session::get('mhs_keu_smtin');
                if ( !empty(Session::get('mhs_keu_smtin')) && Session::get('mhs_keu_smtin') != 'all' ) {
                    $mhs->where('m2.semester_mulai', $smtin);
                }
            }

            // Filter prodi
            if ( Session::get('mhs_keu_prodi') == 'all' ){
                $mhs->whereIn('m2.id_prodi', Sia::getProdiUser());
            } else {
                $mhs->where('m2.id_prodi', Session::get('mhs_keu_prodi'));
            }

            // Filter jns
            $mhs->where('m2.id_jenis_keluar', Session::get('mhs_keu_status'));

            // Status Bayar
            if ( Session::get('mhs_keu_bayar') == 'BB' ) {
                $mhs->whereNotIn('m2.id',
                        DB::table('krs_status')->where('id_smt', Session::get('mhs_keu_smt'))
                            ->pluck('id_mhs_reg'));
            }

            if ( Session::get('mhs_keu_bayar') == 'SB' ) {
                $mhs->whereIn('m2.id',
                        DB::table('krs_status')->where('id_smt', Session::get('mhs_keu_smt'))
                            ->pluck('id_mhs_reg'));
            }

            if ( !empty($r->cari) ) {
                $mhs->where(function($q)use($r){
                    $q->where('m2.nim', 'like', '%'.$r->cari.'%')
                        ->orWhere('m1.nm_mhs', 'like', '%'.$r->cari.'%');
                });
            }

            // $lulus = DB::table('mahasiswa_reg')
            //             ->where('id_jenis_keluar', '<>', 0)
            //             ->where('semester_keluar', '')
            
            // $mhs->where('m2.semester_keluar', '', Session::get('mhs_keu_smt'));
            $mhs->where(function($q)use($r){
                $q->where('m2.id_jenis_keluar', 0)
                    ->orWhere('m2.semester_keluar', '>=', Session::get('mhs_keu_smt'));
            });

            $data['mahasiswa'] = $mhs->orderBy('m2.nim','desc')->paginate(10);

            $data['semester'] = Sia::listSemester();

            return view('keuangan.index', $data);
        }

        private function updateStatusBayar($r)
        {
            if ( $r->update_status_bayar == 'N' ) {

                $rule = DB::table('pembayaran')
                        ->where('id_mhs_reg', $r->id_mhs_reg)
                        ->where('id_smt', Session::get('mhs_keu_smt'))
                        ->where('id_jns_pembayaran', '<>', 99)
                        ->count();

                $rule2 = DB::table('nilai as n')
                            ->leftJoin('jadwal_kuliah as jdk', 'jdk.id','n.id_jdk')
                                ->where('n.id_mhs_reg', $r->id_mhs_reg)
                                ->where('jdk.id_smt', Session::get('mhs_keu_smt'))
                                ->where('jdk.jenis', 1)
                                ->count();

                if ( $rule > 0 ) {

                    Rmt::error('Data tidak dapat dihapus karena telah terdapat history pembayaran. Hapus history pembayaran terlebih dahulu..!');
                    
                } elseif ( $rule2 > 0 ) {
                    Rmt::error('Data tidak dapat dihapus karena telah mengisi KRS. Untuk menghapusnya, hapus KRS pada semester ini..! Hubungi Bagian Akademik..!');

                } else {

                    \App\KrsStatus::where('id_mhs_reg', $r->id_mhs_reg)
                        ->where('id_smt', Session::get('mhs_keu_smt'))
                        ->where('jenis', 'KULIAH')
                        ->delete();
                    Rmt::success('Berhasil mengubah data');
                }

            } else {

                $krs = \App\KrsStatus::where('id_mhs_reg', $r->id_mhs_reg)
                            ->where('id_smt', Session::get('mhs_keu_smt'))
                            ->where('jenis', 'KULIAH')
                            ->count();

                if ( $krs == 0 ) {
                    $data_krs = new \App\KrsStatus;
                    $data_krs->id_mhs_reg = $r->id_mhs_reg;
                    $data_krs->id_smt = Session::get('mhs_keu_smt');
                    $data_krs->save();
                }

                // Insert kartu ujian
                $this->insertKartuUjian($r->id_mhs_reg, Session::get('mhs_keu_smt'));

                Rmt::success('Berhasil menyimpan data');
            }

            return true;
        }


        // Fungsi ini hanya untuk s2
        public function setAllSudahBayar(Request $r)
        {

            $in_krs_status = DB::table('krs_status')
                            ->where('id_smt', Session::get('mhs_keu_smt'))
                            ->pluck('id_mhs_reg');
            $mahasiswa = Mahasiswareg::where('id_jenis_keluar', 0)
                            ->where('id_prodi', 61101)
                            ->whereNotIn('id', $in_krs_status)->get();

            if ( count($mahasiswa) <= 0 ) {
                return Response::json(['Semua status mahasiswa sudah bayar.'], 422);
            }
            
            foreach( $mahasiswa as $mhs ) {

                $krs = \App\KrsStatus::where('id_mhs_reg', $mhs->id)
                            ->where('id_smt', Session::get('mhs_keu_smt'))
                            ->where('jenis', 'KULIAH')
                            ->count();

                if ( $krs == 0 ) {
                    $data_krs = new \App\KrsStatus;
                    $data_krs->id_mhs_reg = $mhs->id;
                    $data_krs->id_smt = Session::get('mhs_keu_smt');
                    $data_krs->save();
                }

            }

            Rmt::success('Berhasil menyimpan data');

            return Response::json(['error' => 0, 'msg' => '']);
        }

        private function setSessionFilter()
        {
            Session::set('mhs_keu_smt', Sia::sessionPeriode());
            Session::set('mhs_keu_status', '0');
            Session::set('mhs_keu_bayar', 'ALL');
            Session::set('mhs_keu_prodi', 'all');
            Session::set('mhs_keu_angkatan', 'all');
            Session::set('mhs_keu_smtin', 'all');
        }

        public function detail(Request $r, $id)
        {
            if ( !Session::has('smt_in_uang') ) {
                Session::put('smt_in_uang', Sia::sessionPeriode());
            }

            $data = $this->mhsHistory($r, $id);

            $this->setSessionMhs($data['mhs']);

            $data['semester'] = DB::table('semester')
                                    ->whereBetween('id_smt', [$data['mhs']->semester_mulai, Sia::semesterBerjalan()['id']])
                                    ->orderBy('id_smt','desc')->get();

            $data['briva'] = $this->getBriva();

            $data['transaksi'] = Briva::where('nim', Sia::sessionMhs('nim'))
                ->where('status', 'Y')
                ->orderBy('id','desc')
                ->take(5)
                ->get();

            return view('keuangan.detail', $data);
        }

        private function setSessionMhs($data)
        {
            Session::set('nim', $data->nim);
            Session::set('nama', $data->nm_mhs);
            Session::set('id_mhs_reg', $data->id_mhs_reg);
        }

        private function getBriva()
        {
            $today = Carbon::now();
            $data = Briva::where('nim', Sia::sessionMhs('nim'))
                    ->where('status', 'N')
                    ->where('exp_date', '>', $today)
                    ->orderBy('id','desc')
                    ->get();

            return $data;
        }

        private function mhsHistory($r, $id)
        {
            if ( !empty($r->smt) ) {
                $smt = $r->smt;
            } else {
                $smt = Sia::sessionPeriode();
            }

            $data['pembayaran'] = Sia::historyBayar($smt)
                                    ->where('p.id_mhs_reg', $id)
                                    ->orderBy('p.tgl_bayar')->get();

            $data['mhs'] = Sia::mahasiswa()
                            ->select('m1.nm_mhs','m2.id_prodi','p.jenjang','p.nm_prodi','m2.nim',
                                'm2.id as id_mhs_reg','m2.semester_mulai',
                                DB::raw('(select status_mhs from '.Sia::prefix().'aktivitas_kuliah
                                        where id_smt='.$smt.'
                                        and id_mhs_reg=\''.$id.'\') as akm'))
                            ->where('m2.id', $id)
                            ->first();

            $data['tagihan'] = DB::table('biaya_kuliah')
                        ->where('tahun', substr($data['mhs']->semester_mulai,0,4))
                        ->where('id_prodi', $data['mhs']->id_prodi)->first();

            return $data;
        }
        
        public function dataPembayaran(Request $r)
        {
            $mhs = Mahasiswareg::find($r->id_mhs_reg);

            $history = Pembayaran::where('id_mhs_reg', $r->id_mhs_reg)
                        ->where('id_smt', Session::get('mhs_keu_smt'))
                        ->where('id_jns_pembayaran',0)->orderBy('tgl_bayar')->get();

            $tagihan = DB::table('biaya_kuliah')
                        ->where('tahun', substr($r->semester_mulai,0,4))
                        ->where('id_prodi', $mhs->id_prodi)
                        ->first();

            $potongan = \App\PotonganBiayaKuliah::where('id_mhs_reg', $r->id_mhs_reg)->get();

            $jml_potongan = 0;
            $total_bayar = 0;
            $sisa_bayar = 0;
            $total_tagihan = 0;
            ?>
                <table class="table table-bordered">
                    <thead class="custom">
                        <tr>
                            <th colspan="2">Tagihan Pembayaran</th>
                        </tr>
                        <tr>
                            <th>Nama Tagihan</th>
                            <th>Nominal</th>
                        </tr>
                    </thead>

                    <?php if ( !empty($tagihan) ) { ?>
                        <tbody>

                            <?php if ( Sia::posisiSemesterMhs($r->semester_mulai) > 1 ) { ?>

                                <tr>
                                    <td>BPP</td>
                                    <td>Rp <?= Rmt::rupiah($tagihan->bpp) ?></td>
                                </tr>
                                <?php foreach( $potongan as $pot ) { ?>
                                    <?php if ( $pot->jenis_potongan == 'SPP' ) continue ?>
                                    <tr>
                                        <td>Potongan <?= $pot->jenis_potongan ?></td>
                                        <td><?= 'Rp '.Rmt::rupiah($pot->potongan) ?></td>
                                    </tr>
                                    <?php $jml_potongan += $pot->potongan ?>
                                <?php } ?>
                                <?php $total_tagihan = $tagihan->bpp ?>

                            <?php } else { ?>

                                <tr>
                                    <td>BPP</td>
                                    <td>Rp <?= Rmt::rupiah($tagihan->bpp) ?></td>
                                </tr>
                                <tr>
                                    <td>SPP</td>
                                    <td>Rp <?= Rmt::rupiah($tagihan->spp) ?></td>
                                </tr>
                                <tr>
                                    <td>Seragam</td>
                                    <td>Rp <?= Rmt::rupiah($tagihan->seragam) ?></td>
                                </tr>
                                <tr>
                                    <td>Lain-lain</td>
                                    <td><?= empty($tagihan->lainnya) ? '0' : 'Rp '.Rmt::rupiah($tagihan->lainnya) ?></td>
                                </tr>
                                <?php foreach( $potongan as $pot ) { ?>
                                    <tr>
                                        <td>Potongan <?= $pot->jenis_potongan ?></td>
                                        <td><?= 'Rp '.Rmt::rupiah($pot->potongan) ?></td>
                                    </tr>
                                    <?php $jml_potongan += $pot->potongan ?>
                                <?php } ?>

                                <?php $total_tagihan = $tagihan->bpp + $tagihan->spp + $tagihan->seragam + $tagihan->lainnya ?>

                            <?php } ?>

                            <tr>
                                <td><b>TOTAL</b></td>
                                <td><b>Rp <?= Rmt::rupiah($total_tagihan - $jml_potongan) ?></b></td>
                            </tr>
                            
                        </tbody>
                    <?php } else { ?>
                        <tr><td colspan="2">Tagihan belum diinput pada tahun masuk mahasiswa ini</td></tr>
                    <?php } ?>
                </table>
                <br>
                <table class="table table-bordered">
                    <thead class="custom">
                        <tr>
                            <th colspan="3">History Pembayaran</th>
                        </tr>
                        <tr>
                            <th><b>Tanggal</b></th>
                            <th><b>Jml bayar</b></th>
                            <th width="80"><b>Aksi</b></th>
                        </tr>
                    </thead>
                    <?php if ( $history->count() == 0 ) { ?>
                        <tr><td colspan="3">Belum ada history</td></tr>
                    <?php } else { ?>
                        <tbody>

                            <?php foreach( $history as $his ) { ?>
                                <tr>
                                    <td><?= Carbon::parse($his->tgl_bayar)->format('d/m/Y') ?></td>
                                    <td>Rp <?= Rmt::rupiah($his->jml_bayar) ?></td>
                                    <td>
                                        <a href="javascript:;" onclick="edit('<?= $his->id ?>', '<?= $r->id_mhs_reg ?>')" title="ubah" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i></a> &nbsp;
                                        
                                        <a href="javascript:;" onclick="hapus('<?= $his->id ?>','<?= $r->id_mhs_reg ?>')" title="hapus" class="btn btn-danger btn-xs"><i class="fa fa-times"></i></a>
                                    </td>
                                </tr>
                                <?php $total_bayar += $his->jml_bayar ?>
                            <?php } ?>

                            <tr>
                                <td><b>TOTAL</b></td>
                                <td colspan="2"><b>Rp <?= Rmt::rupiah($total_bayar) ?></b></td>
                            </tr>
                            <?php if ( !empty($tagihan) ) { ?>
                                <tr><td colspan="3"></td></tr>
                                <tr>
                                    <td><b>SISA PEMBAYARAN</b></td>
                                    <td colspan="2"><b>Rp <?= Rmt::rupiah($total_tagihan - $total_bayar - $jml_potongan) ?></b></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    <?php } ?>
                </table>
            <?php
        }

        public function impor(Request $request)
        {
            $this->validate($request, [
                'file' => 'required'
            ]);

            if ( !is_dir(storage_path().'/tmp') ) {
                mkdir(storage_path().'/tmp');
            }

            $nama_file = $request->file('file')->getClientOriginalName();
            $request->file('file')->move(storage_path().'/tmp', $nama_file);

            try {

                Excel::load(storage_path().'/tmp/'.$nama_file, function($reader)use(&$errors,&$data,&$sukses) {
                    $results = $reader->get();

                    $errors = [];
                    $sukses = 0;

                    DB::transaction(function()use($results,&$errors,&$sukses){

                        foreach( $results as  $r ) {
                            $mhs = Mahasiswareg::select('id')->where('nim', trim($r->nim))->first();

                            if ( empty($mhs) ) {

                                $errors[] = $r->nim.' tidak ditemukan di siakad';

                            } else {

                                // Find pembayaran serupa
                                $rule = DB::table('pembayaran')
                                        ->where('id_mhs_reg', $mhs->id)
                                        ->where('tgl_bayar', Carbon::parse($r->tgl_bayar)->format('Y-m-d'))
                                        ->where('jml_bayar', $r->jml_bayar)->count();
                                
                                if ( $rule > 0 ) {
                                    $errors[] = $r->nim.'|'.$r->tgl_bayar.'|'.$r->jml_bayar.' Telah ada';
                                    continue;
                                }

                                $byr = new \App\Pembayaran;
                                $byr->id_smt = $r->id_smt;
                                $byr->id_mhs_reg = $mhs->id;
                                $byr->tgl_bayar = Carbon::parse($r->tgl_bayar)->format('Y-m-d');
                                $byr->jml_bayar = $r->jml_bayar;
                                $byr->id_bank = $r->bank;
                                $byr->jenis_bayar = $r->jenis_bayar;
                                $byr->ket = $r->ket;
                                $byr->save();

                                $sukses++;

                                $krs = \App\KrsStatus::where('id_mhs_reg', $mhs->id)
                                        ->where('id_smt', $r->id_smt)
                                        ->where('jenis', 'KULIAH')
                                        ->count();

                                if ( $krs == 0 ) {
                                    $data_krs = new \App\KrsStatus;
                                    $data_krs->id_mhs_reg = $mhs->id;
                                    $data_krs->id_smt = $r->id_smt;;
                                    $data_krs->save();
                                }

                            }
                        }

                    });

                });


            } catch(\Exception $e) {
                $response = ['error' => 1, 'msg' => $e->getMessage()];
            }


            if ( file_exists(storage_path().'/tmp/'.$nama_file) ) {
                unlink(storage_path().'/tmp/'.$nama_file);
            }

            $response = ['error' => 0, 'msg' =>''];

            if ( count($errors) > 0 ) {
                Session::flash('errors_impor', $errors);
            }

            Rmt::success($sukses == 0 ? 'Tidak ada data pembayaran dimasukkan' : $sukses.' data pembayaran dimasukkan');
            return Response::json($response,200);
        }

        public function store(Request $r)
        {
            $this->validate($r, [
                'jml_bayar' => 'required',
                'tgl_bayar' => 'required',
            ]);

            try {

                DB::transaction(function()use($r){

                    $jml_bayar = str_replace('.', '', $r->jml_bayar);
                    $jml_bayar2 = str_replace(',', '', $jml_bayar);

                    $data = new Pembayaran;
                    $data->id_smt = Session::get('mhs_keu_smt');
                    $data->id_mhs_reg = $r->id_mhs_reg;
                    $data->tgl_bayar = Carbon::parse($r->tgl_bayar)->format('Y-m-d');
                    $data->jml_bayar = $jml_bayar2;
                    $data->id_bank = $r->bank;
                    $data->jenis_bayar = $r->jenis_bayar;
                    $data->ket = $r->ket;
                    $data->save();

                    $krs = \App\KrsStatus::where('id_mhs_reg', $r->id_mhs_reg)
                            ->where('id_smt', Session::get('mhs_keu_smt'))
                            ->count();

                    if ( $krs == 0 ) {
                        $data_krs = new \App\KrsStatus;
                        $data_krs->id_mhs_reg = $r->id_mhs_reg;
                        $data_krs->id_smt = Session::get('mhs_keu_smt');
                        $data_krs->save();
                    }

                    // Insert kartu ujian
                    $this->insertKartuUjian($r->id_mhs_reg, Session::get('mhs_keu_smt'));

                });

             } 
             catch(\Exception $e)
             {
                return Response::json(['error' => 1, 'msg' => $e->getMessage()]);
             }

             Rmt::success('Berhasil menyimpan data');
             return Response::json(['error' => 0, 'msg' => ''], 200);
        }

        public function edit(Request $r)
        {
            $mhs = \App\Mahasiswareg::find($r->id_mhs_reg);
            $pmb = Pembayaran::find($r->id); ?>

            <input type="hidden" name="id" value="<?= $r->id ?>">
            <input type="hidden" name="id_mhs_reg" value="<?= $r->id_mhs_reg ?>">
            <input type="hidden" name="smt_mulai" value="<?= $mhs->semester_mulai ?>">

            <table class="table" width="100%" border="0">
                <tr>
                    <td style="padding: 10px 0">Mahasiswa</td>
                    <td><?= $mhs->nim .' '.$mhs->mhs->nm_mhs ?></td>
                </tr>
                <tr>
                    <td style="padding: 10px 0">Semester</td>
                    <td><?= Sia::sessionPeriode('nama') ?></td>
                </tr>
                <tr>
                    <td>Jumlah Bayar</td>
                    <td>
                        <input type="text" name="jml_bayar" class="form-control" value="<?= Rmt::rupiah($pmb->jml_bayar) ?>">
                    </td>
                </tr>
                <tr>
                    <td>Tanggal Bayar</td>
                    <td><input type="date" name="tgl_bayar" class="form-control mw-2" value="<?= $pmb->tgl_bayar ?>"></td>
                </tr>
                <tr>
                    <td>Jenis Bayar</td>
                    <td>
                        <select name="jenis_bayar" class="form-control" onchange="jenisBayar(this.value)">
                            <option value="BANK" <?= $pmb->jenis_bayar == 'BANK' ? 'selected':'' ?>>BANK</option>
                            <option value="LAINNYA" <?= $pmb->jenis_bayar == 'LAINNYA' ? 'selected':'' ?>>LAINNYA</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Keterangan</td>
                    <td><textarea name="ket" class="form-control"><?= $pmb->ket ?></textarea></td>
                </tr>
                <tr>
                    <td>Nama Bank</td>
                    <td>
                        <select name="bank" class="form-control" id="bank-edit"<?= $pmb->jenis_bayar == 'LAINNYA' ? ' disabled=""':'' ?>>
                            <?php foreach( Sia::bank() as $b ) { ?>
                                <option value="<?= $b->id ?>" <?= $b->id == $pmb->id_bank ? 'selected':'' ?>><?= $b->nm_bank ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
            </table>
        <?php
        }

        public function update(Request $r)
        {
            $this->validate($r, [
                'jml_bayar' => 'required',
                'tgl_bayar' => 'required',
            ]);

            try {

                DB::transaction(function()use($r){

                    $jml_bayar = str_replace('.', '', $r->jml_bayar);
                    $jml_bayar2 = str_replace(',', '', $jml_bayar);

                    $data = Pembayaran::find($r->id);
                    $data->tgl_bayar = Carbon::parse($r->tgl_bayar)->format('Y-m-d');
                    $data->jml_bayar = $jml_bayar2;
                    $data->id_bank = $r->bank;
                    $data->jenis_bayar = $r->jenis_bayar;
                    $data->ket = $r->ket;
                    $data->save();

                });

             } 
             catch(\Exception $e)
             {
                return Response::json(['error' => 1, 'msg' => $e->getMessage()]);
             }

             Rmt::success('Berhasil menyimpan data');
             return Response::json(['error' => 0, 'msg' => 'update', 'id_mhs_reg' => $r->id_mhs_reg, 'smt_mulai' => $r->smt_mulai], 200);
        }

        public function delete($id)
        {
            Pembayaran::find($id)->delete();

            return Response::json(['error' => 0],200);
        }

        private function historyPembayaran($r)
        {
            $smt = NULL;
            $jenis_bayar = NULL;

            if ( !empty($r->jnsbayar) ) {
                $jenis_bayar = Session::get('pr_jenis_bayar');
                $smt = Session::get('pr_smt');
            } elseif ( !empty($r->bayar_sp) ) {
                $jenis_bayar = 99;
                $smt = Session::get('sp_smt');
            }
            else {
                $smt = Session::get('mhs_keu_smt');
            }

            $data = Sia::historyBayar($smt, $jenis_bayar)
                    ->leftJoin('mahasiswa_reg as m1', 'm1.id', 'p.id_mhs_reg')
                    ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->leftJoin('prodi as pr', 'pr.id_prodi', 'm1.id_prodi')
                    ->leftJoin('semester as smt', 'smt.id_smt', 'p.id_smt')
                    ->select('p.*','b.nm_bank','m2.nm_mhs','m1.nim', 'pr.jenjang',
                            'pr.nm_prodi', 'm1.semester_mulai', 'smt.nm_smt')
                    ->whereBetween('p.tgl_bayar', [$r->tgl1, $r->tgl2]);

            // Filter prodi
            if ( Session::get('mhs_keu_prodi') == 'all' ){
                $data->whereIn('m1.id_prodi', Sia::getProdiUser());
            } else {
                $data->where('m1.id_prodi', Session::get('mhs_keu_prodi'));
            }
            
            $data = $data->orderBy('p.tgl_bayar')->get();

            return $data;

        }

        public function cetak(Request $r)
        {
            if ( $r->tgl1 > $r->tgl2 ) {
                echo 'Rentang tanggal salah';
                return;
            }

            $data['pembayaran'] = $this->historyPembayaran($r);

            return view('keuangan.cetak', $data);
        }

        public function ekspor(Request $r)
        {
            if ( $r->tgl1 > $r->tgl2 ) {
                echo 'Rentang tanggal salah';
                return;
            }

            $data['pembayaran'] = $this->historyPembayaran($r);

            try {
                Excel::create('Laporan Pembayaran', function($excel)use($data) {

                    $excel->sheet('New sheet', function($sheet)use($data) {

                        $sheet->loadView('keuangan.excel', $data);

                    });

                })->download('xlsx');;
            } catch(\Exception $e) {
                echo $e->getMessage();
            }

        }

        public function cetakLangsung(Request $r)
        {
            $mhs = Sia::MhsKeuangan(Session::get('mhs_keu_smt'));

            // Filter angkatan
            if ( Session::get('mhs_keu_angkatan') != 'all' ) {
                $mhs->whereRaw('left('.Sia::prefix().'m2.nim,4)='.Session::get('mhs_keu_angkatan'));
            }

            // Filter smt masuk ganjil/genap
            if ( Session::get('mhs_keu_angkatan') != 'all') {
                $smtin = Session::get('mhs_keu_angkatan').Session::get('mhs_keu_smtin');
                if ( !empty(Session::get('mhs_keu_smtin')) && Session::get('mhs_keu_smtin') != 'all' ) {
                    $mhs->where('m2.semester_mulai', $smtin);
                }
            }

            // Filter prodi
            if ( Session::get('mhs_keu_prodi') == 'all' ){
                $mhs->whereIn('m2.id_prodi', Sia::getProdiUser());
            } else {
                $mhs->where('m2.id_prodi', Session::get('mhs_keu_prodi'));
            }

            // Filter jns
            $mhs->where('m2.id_jenis_keluar', Session::get('mhs_keu_status'));

            if ( Session::get('mhs_keu_bayar') == 'BB' ) {
                $mhs->whereNotIn('m2.id',
                        DB::table('krs_status')->where('id_smt', Session::get('mhs_keu_smt'))
                            ->pluck('id_mhs_reg'));
            }

            if ( Session::get('mhs_keu_bayar') == 'SB' ) {
                $mhs->whereIn('m2.id',
                        DB::table('krs_status')->where('id_smt', Session::get('mhs_keu_smt'))
                            ->pluck('id_mhs_reg'));
            }

            $data['mahasiswa'] = $mhs->orderBy('m2.nim','desc')->get();

            $data['smt'] = DB::table('semester')->where('id_smt', Session::get('mhs_keu_smt'))->first();

            return view('keuangan.cetak-langsung', $data);
        }

        public function cetakDetail(Request $r, $id)
        {
            if ( !Session::has('smt_in_uang') ) {
                Session::put('smt_in_uang', Sia::sessionPeriode());
            }

            $data = $this->mhsHistory($r, $id);

            return view('keuangan.cetak-detail', $data);
        }


    /* Pembayaran praktek */

        public function praktek(Request $r)
        {
            // Set Filter
                if ( !empty($r->smt) ) {
                    Session::set('pr_smt', $r->smt);
                }

                if ( !empty($r->jnsbayar) ) {
                    Session::set('pr_jenis_bayar', $r->jnsbayar);
                }

                if ( !empty($r->prodi) ) {
                    Session::set('pr_prodi', $r->prodi);
                }
                if ( !empty($r->cari) ) {
                    Session::set('pr_cari', $r->cari);
                } else {
                    Session::pull('pr_cari');
                }

                if ( !Session::has('pr_smt') ) {
                    $this->setSessionPraktek();
                }
            // End set

            $mhs = DB::table('pembayaran as p')
                    ->leftJoin('mahasiswa_reg as m1', 'p.id_mhs_reg', 'm1.id')
                    ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->leftJoin('prodi as pr', 'm1.id_prodi', 'pr.id_prodi')
                    ->leftJoin('jenis_pembayaran as jp', 'p.id_jns_pembayaran', 'jp.id_jns_pembayaran')
                    ->select('p.*','m1.nim','m2.nm_mhs','pr.jenjang','pr.nm_prodi','p.tgl_bayar','p.jml_bayar','jp.ket')
                    ->where('p.id_smt', Session::get('pr_smt'))
                    ->whereNotIn('p.id_jns_pembayaran', [0,99]);

            $this->filter($mhs);

            $data['mahasiswa'] = $mhs->orderBy('p.tgl_bayar', 'desc')->paginate(10);

            $data['semester'] = Sia::listSemester();

            return view('keuangan.praktek', $data);

        }

        private function filter($mhs)
        {
            // Filter prodi
                if ( Session::get('pr_prodi') == 'all' ){
                    $mhs->whereIn('m1.id_prodi', Sia::getProdiUser());
                } else {
                    $mhs->where('m1.id_prodi', Session::get('pr_prodi'));
                }

                // Jenis bayar
                if ( Session::get('pr_jenis_bayar') != 'all' ) {
                    $mhs->where('p.id_jns_pembayaran', Session::get('pr_jenis_bayar'));
                }

                // Cari
                if ( Session::has('pr_cari') ) {
                    $mhs->where(function($q){
                        $q->where('m1.nim', 'like', '%'.Session::get('pr_cari').'%')
                            ->orWhere('m2.nm_mhs', 'like', '%'.Session::get('pr_cari').'%');
                    });
                }

                
        }

        private function setSessionPraktek()
        {
            Session::set('pr_smt', Sia::sessionPeriode());
            Session::set('pr_prodi', 'all');
            Session::set('pr_angkatan', 'all');
            Session::set('pr_jenis_bayar', 'all');
        }

        public function praktekDetail(Request $r, $id)
        {
            if ( !empty($r->smt) && !empty($r->jenisbayar) ) {
                $smt = $r->smt;
                $jenisbayar = $r->jenisbayar;
            } elseif ( !empty($r->smt) && !empty($r->bayarsp) ) {
                $smt = $r->smt;
                $jenisbayar = 99;
            } else {
                $smt = Sia::sessionPeriode();
                $jenisbayar = $r->jenisbayar;
            }

            $data['pembayaran'] = Sia::historyBayar($smt, $jenisbayar)
                                    ->where('p.id_mhs_reg', $id)
                                    ->orderBy('p.tgl_bayar')->get();

            $data['mhs'] = Sia::mahasiswa()
                            ->select('m1.nm_mhs','p.jenjang','p.nm_prodi','m2.nim',
                                'm2.id as id_mhs_reg','m2.semester_mulai',
                                DB::raw('(select ket from '.Sia::prefix().'jenis_pembayaran
                                        where id_jns_pembayaran='.$jenisbayar.') as jenis_bayar'))
                            ->where('m2.id', $id)
                            ->first();
                                    
            return view('keuangan.praktek-detail', $data);
        }

        public function mahasiswa(Request $r)
        {
            $param = $r->input('query');
            if ( !empty($param) ) {
                $mahasiswa = Sia::mahasiswa()->whereIn('m2.id_prodi', Sia::getProdiUser())
                                ->where(function($q)use($param){
                                    $q->where('m2.nim', 'like', '%'.$param.'%')
                                        ->orWhere('m1.nm_mhs', 'like', '%'.$param.'%');
                                })->select('m2.id','m2.nim','m1.nm_mhs')->take(10)->get();
            } else {
                $mahasiswa = Sia::mahasiswa()->whereIn('m2.id_prodi', Sia::getProdiUser())
                                ->select('m2.id','m2.nim','m1.nm_mhs')->take(10)->get();
            }

            $data = [];
            foreach( $mahasiswa as $r ) {
                $data[] = ['data' => $r->id, 'value' => $r->nim.' - '.$r->nm_mhs];
            }
            $response = ['query' => 'Unit', 'suggestions' => $data];
            return Response::json($response,200);
        }


        public function praktekStore(Request $r)
        {
            $this->validate($r, [
                'mahasiswa' => 'required',
                'jml_bayar' => 'required',
                'tgl_bayar' => 'required',
            ]);

            try {

                DB::transaction(function()use($r){

                    $jml_bayar = str_replace('.', '', $r->jml_bayar);
                    $jml_bayar2 = str_replace(',', '', $jml_bayar);

                    $data = new Pembayaran;
                    $data->id_smt = Session::get('pr_smt');
                    $data->id_mhs_reg = $r->mahasiswa;
                    $data->tgl_bayar = Carbon::parse($r->tgl_bayar)->format('Y-m-d');
                    $data->jml_bayar = $jml_bayar2;
                    $data->id_bank = $r->bank;
                    $data->jenis_bayar = $r->tempat_bayar;
                    $data->ket = $r->ket;
                    $data->id_jns_pembayaran = $r->jenis_bayar;
                    $data->save();

                });

             } 
             catch(\Exception $e)
             {
                return Response::json(['error' => 1, 'msg' => $e->getMessage()]);
             }

             Rmt::success('Berhasil menyimpan data');
             return Response::json(['error' => 0, 'msg' => ''], 200);
        }

        public function praktekDelete(Request $r)
        {
            // Trigger code
            // CREATE TRIGGER pembayaran_deleted_moved 
            // AFTER DELETE ON pembayaran
            // FOR EACH ROW
            //     INSERT INTO pembayaran_deleted(id_smt, id_mhs_reg, id_jns_pembayaran, tgl_bayar, jml_bayar, ket, created_at) VALUES (OLD.id_smt, OLD.id_mhs_reg, OLD.id_jns_pembayaran, OLD.tgl_bayar, OLD.jml_bayar, OLD.ket, OLD.created_at);

            DB::table('pembayaran')->where('id', $r->id)->delete();

            Rmt::success('Berhasil menghapus data');

            return redirect()->back();
        }

        public function praktekCetak(Request $r)
        {
            if ( $r->tgl1 > $r->tgl2 ) {
                echo 'Rentang tanggal salah';
                return;
            }

            $bayar = DB::table('pembayaran as p')
                    ->leftJoin('bank as b', 'p.id_bank', 'b.id')
                    ->leftJoin('mahasiswa_reg as m1', 'm1.id', 'p.id_mhs_reg')
                    ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->leftJoin('prodi as pr', 'pr.id_prodi', 'm1.id_prodi')
                    ->leftJoin('semester as smt', 'smt.id_smt', 'p.id_smt')
                    ->select('p.*','b.nm_bank','m2.nm_mhs','m1.nim', 'pr.jenjang',
                            'pr.nm_prodi', 'm1.semester_mulai', 'smt.nm_smt')
                    ->whereBetween('p.tgl_bayar', [$r->tgl1, $r->tgl2])
                    ->whereNotIn('p.id_jns_pembayaran',[0,99]);

            $data['pembayaran'] = $bayar->orderBy('tgl_bayar')->get();

            return view('keuangan.praktek-cetak', $data);
        }

        public function praktekEkspor(Request $r)
        {
            if ( $r->tgl1 > $r->tgl2 ) {
                echo 'Rentang tanggal salah';
                return;
            }

            $bayar = DB::table('pembayaran as p')
                    ->leftJoin('bank as b', 'p.id_bank', 'b.id')
                    ->leftJoin('mahasiswa_reg as m1', 'm1.id', 'p.id_mhs_reg')
                    ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->leftJoin('prodi as pr', 'pr.id_prodi', 'm1.id_prodi')
                    ->leftJoin('semester as smt', 'smt.id_smt', 'p.id_smt')
                    ->select('p.*','b.nm_bank','m2.nm_mhs','m1.nim', 'pr.jenjang',
                            'pr.nm_prodi', 'm1.semester_mulai', 'smt.nm_smt')
                    ->whereBetween('p.tgl_bayar', [$r->tgl1, $r->tgl2])
                    ->whereNotIn('p.id_jns_pembayaran',[0,99]);

            $data['pembayaran'] = $bayar->orderBy('tgl_bayar')->get();

            try {
                Excel::create('Laporan Pembayaran', function($excel)use($data) {

                    $excel->sheet('New sheet', function($sheet)use($data) {

                        $sheet->loadView('keuangan.praktek-excel', $data);

                    });

                })->download('xlsx');
            } catch(\Exception $e) {
                echo $e->getMessage();
            }

        }

        public function praktekCetakLangsung(Request $r)
        {

            $mhs = DB::table('pembayaran as p')
                    ->leftJoin('mahasiswa_reg as m1', 'p.id_mhs_reg', 'm1.id')
                    ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->leftJoin('prodi as pr', 'm1.id_prodi', 'pr.id_prodi')
                    ->leftJoin('jenis_pembayaran as jp', 'p.id_jns_pembayaran', 'jp.id_jns_pembayaran')
                    ->select('p.*','m1.nim','m2.nm_mhs','pr.jenjang','pr.nm_prodi','p.tgl_bayar','p.jml_bayar','jp.ket')
                    ->where('p.id_smt', Session::get('pr_smt'))
                    ->whereNotIn('p.id_jns_pembayaran', [0,99]);

            $this->filter($mhs);

            $data['mahasiswa'] = $mhs->get();

            return view('keuangan.praktek-cetak-langsung', $data);
        }

        public function praktekCetakDetail(Request $r, $id)
        {

            $data = $this->mhsHistoryPraktek($r, $id);

            return view('keuangan.praktek-cetak-detail', $data);
        }

    /* SP */

        public function sp(Request $r)
        {
            // Update status bayar
            if ( !empty($r->update_status_bayar) ) {

                if ( $this->updateStatusBayarSp($r) ) return redirect()->back();
            }

            // Set Filter
                if ( !empty($r->smt) ) {
                    Session::set('sp_smt', $r->smt);
                }
                //  else {
                //     Session::set('sp_smt', Sia::sessionPeriode());
                // }

                if ( !empty($r->prodi) ) {
                    Session::set('sp_prodi', $r->prodi);
                }

                if ( !empty($r->bayar) ) {
                    Session::set('sp_bayar', $r->bayar);
                }

                if ( !empty($r->angkatan) ) {
                    Session::set('sp_angkatan', $r->angkatan);
                }

                if ( !Session::has('sp_smt') ) {
                    Session::put('sp_smt', Sia::sessionPeriode());
                    Session::set('sp_bayar', 'all');
                    Session::set('sp_prodi', 'all');
                    Session::set('sp_angkatan', 'all');
                }
            // End set
                $mhs = Sia::MhsKeuanganSp(Session::get('sp_smt'));

                // Filter prodi
                if ( Session::get('sp_prodi') == 'all' ){
                    $mhs->whereIn('m1.id_prodi', Sia::getProdiUser());
                } else {
                    $mhs->where('m1.id_prodi', Session::get('sp_prodi'));
                }

                // Filter angkatan
                if ( Session::get('sp_angkatan') != 'all' ) {
                    $mhs->whereRaw("left(m1.nim,4)='".Session::get('sp_angkatan')."'");
                }

                // Cari
                if ( !empty($r->cari) ) {
                    $mhs->where(function($q)use($r){
                        $q->where('m1.nim', 'like', '%'.$r->cari.'%')
                            ->orWhere('m2.nm_mhs', 'like', '%'.$r->cari.'%');
                    });
                }

                $data['mahasiswa'] = $mhs->orderBy('m1.nim')->get();

            $data['semester'] = Sia::listSemester();

            return view('keuangan.sp.index', $data);
        }

        private function updateStatusBayarSp($r)
        {
            if ( $r->update_status_bayar == 'N' ) {

                $rule = DB::table('pembayaran')
                        ->where('id_mhs_reg', $r->id_mhs_reg)
                        ->where('id_smt', Session::get('sp_smt'))
                        ->where('id_jns_pembayaran', 99)
                        ->count();

                $rule2 = DB::table('nilai as n')
                            ->leftJoin('jadwal_kuliah as jdk', 'jdk.id','n.id_jdk')
                                ->where('n.id_mhs_reg', $r->id_mhs_reg)
                                ->where('jdk.id_smt', Session::get('sp_smt'))
                                ->where('jdk.jenis', 2)
                                ->count();

                if ( $rule > 0 ) {

                    Rmt::error('Data tidak dapat dihapus karena telah terdapat history pembayaran. Hapus history pembayaran terlebih dahulu..!');
                    
                } elseif ( $rule2 > 0 ) {
                    Rmt::error('Data tidak dapat dihapus karena telah mengisi KRS. Untuk menghapusnya, hapus KRS pada semester ini..! Hubungi Bagian Akademik..!');

                } else {

                    \App\KrsStatus::where('id_mhs_reg', $r->id_mhs_reg)
                        ->where('id_smt', Session::get('sp_smt'))
                        ->where('jenis', 'SP')
                        ->delete();
                    Rmt::success('Berhasil mengubah data');
                }

            } else {

                $krs = \App\KrsStatus::where('id_mhs_reg', $r->id_mhs_reg)
                            ->where('id_smt', Session::get('sp_smt'))
                            ->where('jenis', 'SP')
                            ->count();
                if ( $krs == 0 ) {
                    $data_krs = new \App\KrsStatus;
                    $data_krs->id_mhs_reg = $r->id_mhs_reg;
                    $data_krs->id_smt = Session::get('sp_smt');
                    $data_krs->jenis = 'SP';
                    $data_krs->save();
                }

                Rmt::success('Berhasil menyimpan data');
            }

            return true;
        }

        public function spDetail(Request $r, $id)
        {
            $data = $this->mhsHistoryPraktek($r, $id);
                                    
            return view('keuangan.sp.detail', $data);
        }

        public function dataPembayaranSp(Request $r)
        {
            $history = Pembayaran::where('id_mhs_reg', $r->id_mhs_reg)
                        ->where('id_smt', Session::get('sp_smt'))
                        ->where('id_jns_pembayaran', 99)->orderBy('tgl_bayar')->get();

            $ket = '';

            $total_bayar = 0;
            $sisa_bayar = 0;
            ?>

                <table class="table table-bordered">
                    <thead class="custom">
                        <tr>
                            <th colspan="3">History Pembayaran</th>
                        </tr>
                        <tr>
                            <th><b>Tanggal</b></th>
                            <th><b>Jml bayar</b></th>
                            <th width="75"><b>Aksi</b></th>
                        </tr>
                    </thead>
                    <?php if ( $history->count() == 0 ) { ?>
                        <tr><td colspan="3">Belum ada history</td></tr>
                    <?php } else { ?>
                        <tbody>

                            <?php foreach( $history as $his ) { ?>
                                <tr>
                                    <td><?= Carbon::parse($his->tgl_bayar)->format('d/m/Y') ?></td>
                                    <td>Rp <?= Rmt::rupiah($his->jml_bayar) ?></td>
                                    <td>
                                        <a href="javascript:;" onclick="edit('<?= $his->id ?>', '<?= $r->id_mhs_reg ?>')" title="ubah" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i></a> &nbsp;
                                        
                                        <a href="javascript:;" onclick="hapus('<?= $his->id ?>','<?= $r->id_mhs_reg ?>')" title="hapus" class="btn btn-danger btn-xs"><i class="fa fa-times"></i></a>
                                    </td>
                                </tr>
                                <?php $total_bayar += $his->jml_bayar ?>
                            <?php } ?>

                            <tr>
                                <td><b>TOTAL</b></td>
                                <td colspan="2"><b>Rp <?= Rmt::rupiah($total_bayar) ?></b></td>
                            </tr>

                        </tbody>
                    <?php } ?>
                </table>
            <?php
        }

        public function spStore(Request $r)
        {
            $this->validate($r, [
                'jml_bayar' => 'required',
                'tgl_bayar' => 'required',
            ]);

            try {

                DB::transaction(function()use($r){

                    $jml_bayar = str_replace('.', '', $r->jml_bayar);
                    $jml_bayar2 = str_replace(',', '', $jml_bayar);

                    $data = new Pembayaran;
                    $data->id_smt = Session::get('sp_smt');
                    $data->id_mhs_reg = $r->id_mhs_reg;
                    $data->tgl_bayar = Carbon::parse($r->tgl_bayar)->format('Y-m-d');
                    $data->jml_bayar = $jml_bayar2;
                    $data->id_bank = $r->bank;
                    $data->jenis_bayar = $r->jenis_bayar;
                    $data->ket = $r->ket;
                    $data->id_jns_pembayaran = 99;
                    $data->save();

                    $krs = \App\KrsStatus::where('id_mhs_reg', $r->id_mhs_reg)
                            ->where('id_smt', Session::get('sp_smt'))
                            ->where('jenis', 'SP')
                            ->count();

                    if ( $krs == 0 ) {
                        $data_krs = new \App\KrsStatus;
                        $data_krs->id_mhs_reg = $r->id_mhs_reg;
                        $data_krs->id_smt = Session::get('sp_smt');
                        $data_krs->valid = '1';
                        $data_krs->status_krs = '1';
                        $data_krs->jenis = 'SP';
                        $data_krs->save();
                    }

                });

             } 
             catch(\Exception $e)
             {
                return Response::json(['error' => 1, 'msg' => $e->getMessage()]);
             }

             Rmt::success('Berhasil menyimpan data');
             return Response::json(['error' => 0, 'msg' => ''], 200);
        }

        public function spCetak(Request $r)
        {
            if ( $r->tgl1 > $r->tgl2 ) {
                echo 'Rentang tanggal salah';
                return;
            }

            $data['pembayaran'] = $this->historyPembayaran($r);

            return view('keuangan.sp.cetak', $data);
        }

        public function spEkspor(Request $r)
        {
            if ( $r->tgl1 > $r->tgl2 ) {
                echo 'Rentang tanggal salah';
                return;
            }

            $data['pembayaran'] = $this->historyPembayaran($r);

            try {
                Excel::create('Laporan Pembayaran', function($excel)use($data) {

                    $excel->sheet('New sheet', function($sheet)use($data) {

                        $sheet->loadView('keuangan.sp.excel', $data);

                    });

                })->download('xlsx');
            } catch(\Exception $e) {
                echo $e->getMessage();
            }

        }

        public function spCetakLangsung(Request $r)
        {
            $mhs = Sia::MhsKeuanganSp(Session::get('sp_smt'));

            // Filter prodi
            if ( Session::get('sp_prodi') == 'all' ){
                $mhs->whereIn('m1.id_prodi', Sia::getProdiUser());
            } else {
                $mhs->where('m1.id_prodi', Session::get('sp_prodi'));
            }

            // Filter angkatan
            if ( Session::get('sp_angkatan') != 'all' ) {
                $mhs->whereRaw("left(m1.nim,4)='".Session::get('sp_angkatan')."'");
            }

            $data['mahasiswa'] = $mhs->orderBy('m1.nim')->get();

            return view('keuangan.sp.cetak-langsung', $data);
        }

        public function spCetakDetail(Request $r, $id)
        {

            $data = $this->mhsHistoryPraktek($r, $id);

            return view('keuangan.sp.cetak-detail', $data);
        }

    /* End */

    private function insertKartuUjian($id_mhs_reg, $id_smt)
    {
        $mhs = Mahasiswareg::find($id_mhs_reg);

        $biaya = Sia::biayaPerMhs($id_mhs_reg, $mhs->semester_mulai, $mhs->id_prodi);
        $potongan = $this->totalPotonganPerMhs($id_mhs_reg, $mhs->semester_mulai, $id_smt);
        $tagihan = $biaya - $potongan;
        $total_bayar = $this->totalBayar($id_mhs_reg, $id_smt);
        $tunggakan = $tagihan - $total_bayar;

        $persen_bayar = ($total_bayar/$tagihan) * 100;

        // Pembayaran >= 50% maka insert mid 
        if ( $persen_bayar >= 50 && $persen_bayar < 100 ) {

            $data_ku = [
                'id_mhs_reg' => $id_mhs_reg,
                'id_smt' => $id_smt,
                'jenis' => 'UTS'
            ];
            $cek_ku = DB::table('kartu_ujian')
                ->where('id_mhs_reg', $id_mhs_reg)
                ->where('id_smt', $id_smt)
                ->where('jenis', 'UTS')
                ->count();

            if ( $cek_ku == 0 ) {
                DB::table('kartu_ujian')->insert($data_ku);
            }
        
        // Pembayaran 100% insert final dan mid jika belum ada
        } elseif ( $persen_bayar >= 100 ) {

            $data_ku = [
                'id_mhs_reg' => $id_mhs_reg,
                'id_smt' => $id_smt,
                'jenis' => 'UTS'
            ];
            $data_ku2 = [
                'id_mhs_reg' => $id_mhs_reg,
                'id_smt' => $id_smt,
                'jenis' => 'UAS'
            ];

            $cek_ku = DB::table('kartu_ujian')
                ->where('id_mhs_reg', $id_mhs_reg)
                ->where('id_smt', $id_smt)
                ->where('jenis', 'UTS')
                ->count();

            $cek_ku2 = DB::table('kartu_ujian')
                ->where('id_mhs_reg', $id_mhs_reg)
                ->where('id_smt', $id_smt)
                ->where('jenis', 'UAS')
                ->count();

            if ( $cek_ku == 0 ) {
                DB::table('kartu_ujian')->insert($data_ku);
            }
            if ( $cek_ku2 == 0 ) {
                DB::table('kartu_ujian')->insert($data_ku2);
            }
        }
    }

    // private function biayaPerMhs($id_mhs_reg, $smt_mulai, $id_prodi)
    // {
    //     $smt = $smt_mulai - Session::get('mhs_keu_smt');
    //     $smt_mulai = substr($smt_mulai,0,4);

    //     if ( $smt == 0 ) {
    //         $data = DB::table('biaya_kuliah')->selectRaw('bpp+spp+seragam+lainnya as biaya')
    //                     ->where('id_prodi', $id_prodi)
    //                     ->where('tahun', $smt_mulai)->first();
    //     } else {
    //         $data = DB::table('biaya_kuliah')->selectRaw('bpp as biaya')
    //                     ->where('id_prodi', $id_prodi)
    //                     ->where('tahun', $smt_mulai)->first();
    //     }

    //     return empty($data) ? 0 : $data->biaya;

    // }

    private function totalPotonganPerMhs($id_mhs_reg, $id_smt)
    {
        $smt = $id_smt - Session::get('mhs_keu_smt');

        if ( $smt == 0 ) {
            // Semester 1
            $data = DB::table('potongan_biaya_kuliah')
                    ->where('id_mhs_reg', $id_mhs_reg)
                    ->sum('potongan');
        } else {
            $data = DB::table('potongan_biaya_kuliah')
                    ->where('id_mhs_reg', $id_mhs_reg)
                    ->where('jenis_potongan', 'BPP')
                    ->sum('potongan');
        }

        return $data;
    }

    private function totalBayar($id_mhs_reg,$smt)
    {
        $data = DB::table('pembayaran')->where('id_mhs_reg', $id_mhs_reg)
                ->where('id_smt', $smt)->sum('jml_bayar');

        return $data;
    }
}
