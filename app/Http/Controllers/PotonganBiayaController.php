<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB, Sia, Rmt, Response, Session, Carbon, Excel;
use App\PotonganBiayaKuliah;

class PotonganBiayaController extends Controller
{
    /* Pembayaran kuliah */
        public function index(Request $r)
        {

            // Set Filter
                if ( !empty($r->smt) ) {
                    Session::set('pot_smt_masuk', $r->smt);
                }

                if ( !empty($r->prodi) ) {
                    Session::set('pot_prodi', $r->prodi);
                }

                if ( !empty($r->jenis) ) {
                    Session::set('pot_jenis', $r->jenis);
                }

                if ( !Session::has('pot_smt_masuk') ) {
                    $this->setSessionFilter();
                }
            // End set

            $mhs = Sia::PotonganBiaya();

            // Filter smt masuk
            if ( Session::get('pot_smt_masuk') != 'all' ) {
                $mhs->where('m1.semester_mulai', Session::get('pot_smt_masuk'));
            }

            // Filter prodi
            if ( Session::get('pot_prodi') == 'all' ){
                $mhs->whereIn('m1.id_prodi', Sia::getProdiUser());
            } else {
                $mhs->where('m1.id_prodi', Session::get('pot_prodi'));
            }

            if ( Session::get('pot_jenis') != 'all' ) {
                $mhs->where('pb.jenis_potongan', Session::get('pot_jenis'));
            }

            if ( !empty($r->cari) ) {
                $mhs->where(function($q)use($r){
                    $q->where('m1.nim', 'like', '%'.$r->cari.'%')
                        ->orWhere('m2.nm_mhs', 'like', '%'.$r->cari.'%');
                });
            }

            $data['mahasiswa'] = $mhs->orderBy('m1.nim','desc')->paginate(20);

            $data['semester'] = Sia::listSemester();

            return view('keuangan.potongan.index', $data);
        }

        private function setSessionFilter()
        {
            Session::set('pot_smt_masuk', 'all');
            Session::set('pot_status', '0');
            Session::set('pot_prodi', 'all');
            Session::set('pot_jenis', 'all');
        }

        public function mhs(Request $r )
        {
            $param = $r->input('query');
            if ( !empty($param) ) {
                $mahasiswa = DB::table('mahasiswa_reg as m1')
                                ->leftJoin('mahasiswa as m2', 'm1.id_mhs','m2.id')
                                ->where('m1.id_jenis_keluar', 0)
                                ->whereIn('m1.id_prodi', Sia::getProdiUser())
                                ->where(function($q)use($param){
                                    $q->where('m1.nim', 'like', '%'.$param.'%')
                                        ->orWhere('m2.nm_mhs', 'like', '%'.$param.'%');
                                })
                                ->select('m1.id','m1.nim','m2.nm_mhs')->take(10)->get();
            } else {
                $mahasiswa = DB::table('mahasiswa_reg as m1')
                                ->leftJoin('mahasiswa as m2', 'm1.id_mhs','m2.id')
                                ->where('m1.id_jenis_keluar', 0)
                                ->whereIn('m1.id_prodi', Sia::getProdiUser())
                                ->select('m1.id','m1.nim','m2.nm_mhs')->take(10)->get();
            }

            $data = [];
            foreach( $mahasiswa as $r ) {
                $data[] = ['data' => $r->id, 'value' => $r->nim.' - '.$r->nm_mhs];
            }
            $response = ['query' => 'Unit', 'suggestions' => $data];
            return Response::json($response,200);
        }

        public function store(Request $r)
        {
            $this->validate($r, [
                'mahasiswa' => 'required',
                'potongan' => 'required',
            ]);

            $rule = DB::table('potongan_biaya_kuliah')
                        ->where('id_mhs_reg', $r->mahasiswa)
                        ->where('jenis_potongan', $r->jenis_potongan)
                        ->count();

            if ( $rule > 0 ) {
                return Response::json(['error' => 1, 'msg' => 'Potongan '.$r->jenis_potongan.' pada Mahasiswa ini telah ada, silahkan mengubah yang ada']);
            }

            try {

                DB::transaction(function()use($r){

                    $potongan = str_replace('.', '', $r->potongan);
                    $potongan2 = str_replace(',', '', $potongan);
                    $data = [
                        'id_mhs_reg' => $r->mahasiswa,
                        'jenis_potongan' => $r->jenis_potongan,
                        'potongan' => $potongan2,
                        'ket' => $r->ket
                    ];

                    DB::table('potongan_biaya_kuliah')->insert($data);
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
            $mhs = Sia::PotonganBiaya()->where('id_mhs_reg', $r->id_mhs_reg)->first(); ?>

            <?= csrf_field() ?>
            <input type="hidden" name="mahasiswa" value="<?= $mhs->id_mhs_reg ?>">
            <input type="hidden" name="id" value="<?= $mhs->id ?>">

            <table class="table" width="100%" border="0">
                <tr>
                    <td style="padding: 10px 0">Mahasiswa</td>
                    <td><?= $mhs->nim.' - '.$mhs->nm_mhs ?></td>
                </tr>
                <tr>
                    <td>Jenis Potongan</td>
                    <td>
                        <select name="jenis_potongan" class="form-control">
                            <?php foreach( sia::jenisPotongan() as $jp ) { ?>
                                <option value="<?= $jp ?>" <?= $jp == $mhs->jenis_potongan ? 'selected':'' ?>><?= $jp ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Jumlah Potongan</td>
                    <td><input type="text" name="potongan" class="form-control" value="<?= Rmt::rupiah($mhs->potongan) ?>"></td>
                </tr>
                <tr>
                    <td>Keterangan</td>
                    <td><textarea name="ket" class="form-control"><?= $mhs->ket ?></textarea></td>
                </tr>
            </table>

            <script>
                $(function(){

                    $(document).on( "keyup", 'input[name="potongan"]', function( event ) {

                        // When user select text in the document, also abort.
                        var selection = window.getSelection().toString();
                        if ( selection !== '' ) {
                            return;
                        }
                        
                        // When the arrow keys are pressed, abort.
                        if ( $.inArray( event.keyCode, [38,40,37,39] ) !== -1 ) {
                            return;
                        }
                        
                        
                        var $this = $( this );
                        
                        // Get the value.
                        var input = $this.val();

                        var input = input.replace(/[\D\s\._\-]+/g, "");
                        input = input ? parseInt( input, 10 ) : 0;

                        $this.val( function() {
                            return ( input === 0 ) ? "" : input.toLocaleString();
                        } );
                    });

                 });
             </script>

        <?php
        }

        public function update(Request $r)
        {
            $this->validate($r, [
                'potongan' => 'required',
            ]);

            $rule = DB::table('potongan_biaya_kuliah')
                    ->where('id_mhs_reg', $r->mahasiswa)
                    ->where('jenis_potongan', $r->jenis_potongan)
                    ->where('id','<>', $r->id)->count();

            if ( $rule > 0 ) {
                return Response::json(['error' => 1, 'msg' => 'Potongan '.$r->jenis_potongan.' pada Mahasiswa ini telah ada, silahkan mengubah yang ada']);
            }

            try {

                DB::transaction(function()use($r){

                    $potongan = str_replace('.', '', $r->potongan);
                    $potongan2 = str_replace(',', '', $potongan);

                    $data = [
                        'jenis_potongan' => $r->jenis_potongan,
                        'potongan' => $potongan2,
                        'ket' => $r->ket
                    ];

                    DB::table('potongan_biaya_kuliah')
                        ->where('id', $r->id)->update($data);
                });

             } 
             catch(\Exception $e)
             {
                return Response::json(['error' => 1, 'msg' => $e->getMessage()]);
             }

             Rmt::success('Berhasil menyimpan data');
             return Response::json(['error' => 0, 'msg' => ''], 200);
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
                            $mhs = \App\Mahasiswareg::select('id')->where('nim', trim($r->nim))->first();

                            if ( empty($mhs) ) {

                                $errors[] = $r->nim.' tidak ditemukan di siakad';

                            } else {

                                // Find potongan serupa
                                
                                $rule = DB::table('potongan_biaya_kuliah')
                                        ->where('id_mhs_reg', $mhs->id)
                                        ->where('jenis_potongan', $r->jenis_potongan)
                                        ->where('potongan', $r->jumlah_potongan)->count();
                                
                                if ( $rule > 0 ) {
                                    $errors[] = $r->nim.'|'.$r->jenis_potongan.'|'.$r->jumlah_potongan.' Telah ada';
                                    continue;
                                }

                                $data = new PotonganBiayaKuliah;
                                $data->id_mhs_reg = $mhs->id;
                                $data->potongan = $r->jumlah_potongan;
                                $data->jenis_potongan = $r->jenis_potongan;
                                $data->ket = $r->keterangan;
                                $data->save();

                                $sukses++;
                                
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

            Rmt::success($sukses == 0 ? 'Tidak ada data dimasukkan':$sukses.' data potongan dimasukkan');
            return Response::json($response,200);
        }

        public function delete($id)
        {
            DB::table('potongan_biaya_kuliah')
                ->where('id_mhs_reg', $id)->delete();

            Rmt::success('Berhasil menghapus data');
            return redirect()->back();
        }

        public function cetak(Request $r)
        {

            if ( !Session::has('pot_smt_masuk') ) {
                return redirect('pot');
            }

            $mhs = Sia::PotonganBiaya();

            // Filter smt masuk
            $mhs->where('m1.semester_mulai', Session::get('pot_smt_masuk'));

            // Filter prodi
            if ( Session::get('pot_prodi') == 'all' ){
                $mhs->whereIn('m1.id_prodi', Sia::getProdiUser());
            } else {
                $mhs->where('m1.id_prodi', Session::get('pot_prodi'));
            }

            if ( Session::get('pot_jenis') != 'all' ) {
                $mhs->where('pb.jenis_potongan', Session::get('pot_jenis'));
            }
            
            $data['mahasiswa'] = $mhs->orderBy('m1.nim','desc')->get();

            $data['smt'] = DB::table('semester')->where('id_smt', Session::get('pot_smt_masuk'))->first();

            if ( Session::get('pot_prodi') == 'all' ) {
                $data['prodi'] = 'Semua Prodi';
            } else {
                $prodi = DB::table('prodi')->where('id_prodi', Session::get('pot_prodi'))->first();

                $data['prodi'] = $prodi->jenjang.' '.$prodi->nm_prodi;
            }


            return view('keuangan.potongan.cetak', $data);
        }
}
