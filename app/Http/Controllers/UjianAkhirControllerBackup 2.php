<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use DB, Sia, Rmt, Response, Session, Carbon;
use App\Mahasiswareg, App\Penguji, App\Ujianakhir;

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


        $mhs = Sia::ujianAKhir(Session::get('ua_jenis'), Session::get('ua_prodi'), Session::get('ua_semester'), $r->cari);

        $data_mhs = $mhs->orderBy('nim')->get();
        $data_mhs_arr = $data_mhs->toArray();

        // Session mhs yang memprogram untuk dipakai di jadwal seminar
        Session::pull('mhs_in_ujian_akhir');
        Session::put('mhs_in_ujian_akhir', $data_mhs_arr);

        $page = $r->page ? $r->page : 1;
        $perpage = 10;

        $slice = array_slice($data_mhs_arr, $perpage * ($page - 1), $perpage);

        $data['mahasiswa'] = new Paginator($slice, count($data_mhs), $perpage);

        $data['mahasiswa']->setPath($r->url());

        return view('ujian-akhir.index', $data);
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
                        ->first();

        $judul = !empty($ujian) ? $ujian->judul_tmp : '';

        $ketua = Sia::penguji($r->id_mhs_reg, Session::get('ua_jenis'), 'KETUA');
        $sekretaris = Sia::penguji($r->id_mhs_reg, Session::get('ua_jenis'), 'SEKRETARIS');
        $anggota = Sia::penguji($r->id_mhs_reg, Session::get('ua_jenis'), 'ANGGOTA');

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
                    <td>Ketua <span>*</span></td>
                    <td>
                            <div style="position: relative;">
                                <div class="input-icon right"> 
                                    <span id="spinner-autocomplete-1" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                    <input type="text" class="form-control" id="autocomplete-ajax-1" value="<?= !empty($ketua) ? $ketua->nm_dosen : '' ?>">
                                    <input type="hidden" id="ketua" value="<?= !empty($ketua) ? $ketua->id : '' ?>" name="ketua">
                                </div>
                            </div>
                    </td>
                </tr>
                <tr>
                    <td>Sekretaris <span>*</span></td>
                    <td>
                            <div style="position: relative;">
                                <div class="input-icon right"> 
                                    <span id="spinner-autocomplete-2" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                    <input type="text" class="form-control" id="autocomplete-ajax-2" value="<?= !empty($sekretaris) ? $sekretaris->nm_dosen : '' ?>">
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
                                <input type="text" class="form-control" id="autocomplete-ajax-3" value="<?= !empty($anggota) ? $anggota->nm_dosen : '' ?>">
                                <input type="hidden" id="anggota" name="anggota" value="<?= !empty($anggota) ? $anggota->id : '' ?>">
                            </div>
                        </div>
                    </td>
                </tr>

                <?php if ( Session::get('ua_prodi') == '61101' ) { ?>
                    <?php $anggota2 = Sia::penguji($r->id_mhs_reg, Session::get('ua_jenis'), 'ANGGOTA2'); ?>
                    <tr>
                        <td width="160px">Anggota 2</td>
                        <td>
                                <div style="position: relative;">
                                    <div class="input-icon right"> 
                                        <span id="spinner-autocomplete-4" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                        <input type="text" class="form-control" id="autocomplete-ajax-4" value="<?= !empty($anggota2) ? $anggota2->nm_dosen : '' ?>">
                                        <input type="hidden" id="anggota-2" name="anggota_2" value="<?= !empty($anggota2) ? $anggota2->id : '' ?>">
                                    </div>
                                </div>
                        </td>
                    </tr>
                <?php } ?>

                <tr><td colspan="2"><br><b>Jadwal Ujian</b></td></tr>

                <tr>
                    <td>Tanggal</td>
                    <td><input type="date" name="tgl" class="form-control mw-2" value="<?= empty($ujian->tgl_ujian) ? '' : $ujian->tgl_ujian ?>"></td>
                </tr>
                <tr>
                    <td>Pukul</td>
                    <td><input type="text" name="pukul" class="form-control mw-2" value="<?= empty($ujian->pukul) ? '' : $ujian->pukul ?>"></td>
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

            </table>
        </div>

        <script type="text/javascript" src="<?= url('resources') ?>/assets/js/jquery.autocomplete.js"></script>
        <script type="text/javascript" src="<?= url('resources') ?>/assets/js/jquery.mockjax.js"></script>
        <script>
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


                <?php if ( Session::get('ua_prodi') == '61101' ) { ?>
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
                <?php } ?>
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

        try {

            DB::transaction(function()use($r){

                $data[] = ['id_mhs_reg' => $r->id_mhs_reg, 'id_dosen' => $r->ketua, 'jabatan' => 'KETUA'];
                $data[] = ['id_mhs_reg' => $r->id_mhs_reg, 'id_dosen' => $r->sekretaris, 'jabatan' => 'SEKRETARIS'];
                $data[] = ['id_mhs_reg' => $r->id_mhs_reg, 'id_dosen' => $r->anggota, 'jabatan' => 'ANGGOTA'];

                for ( $i = 0; $i < 3; $i++ ) {
                    Penguji::updateOrCreate([ 
                                'id_mhs_reg' => $r->id_mhs_reg,
                                'jenis' => Session::get('ua_jenis'),
                                'jabatan' => $data[$i]['jabatan'],
                            ], $data[$i]);

                    // Simpan juga penguji untuk hasil & tesis saat penguji proposal diinput
                    if ( Session::get('ua_jenis') == 'P' ) {
                        Penguji::updateOrCreate([ 
                                'id_mhs_reg' => $r->id_mhs_reg,
                                'jenis' => 'H',
                                'jabatan' => $data[$i]['jabatan'],
                            ], $data[$i]);
                        Penguji::updateOrCreate([ 
                                'id_mhs_reg' => $r->id_mhs_reg,
                                'jenis' => 'S',
                                'jabatan' => $data[$i]['jabatan'],
                            ], $data[$i]);
                    }
                }

                if ( !empty( $r->anggota_2) ) {
                    $data_4 = ['id_mhs_reg' => $r->id_mhs_reg, 'id_dosen' => $r->anggota_2, 'jabatan' => 'ANGGOTA2'];
                    Penguji::updateOrCreate([ 
                                'id_mhs_reg' => $r->id_mhs_reg,
                                'jenis' => Session::get('ua_jenis'),
                                'jabatan' => 'ANGGOTA2',
                            ], $data_4);
                    if ( Session::get('ua_jenis') == 'P' ) {
                        Penguji::updateOrCreate([ 
                                'id_mhs_reg' => $r->id_mhs_reg,
                                'jenis' => 'H',
                                'jabatan' => 'ANGGOTA2',
                            ], $data_4);
                        Penguji::updateOrCreate([ 
                                'id_mhs_reg' => $r->id_mhs_reg,
                                'jenis' => 'S',
                                'jabatan' => 'ANGGOTA2',
                            ], $data_4);
                    }
                }
                // Ujian Akhir
                $data_ua = [ 
                    'judul_tmp' => $r->judul, 
                    'tgl_ujian' => $r->tgl,
                    'pukul' => $r->pukul,
                    'ruangan' => $r->ruangan,
                    'jenis' => Session::get('ua_jenis')];

                $a = Ujianakhir::updateOrCreate([
                    'id_mhs_reg' => $r->id_mhs_reg,
                    'jenis' => Session::get('ua_jenis'),
                ], $data_ua);

            });

        } catch(\Exception $e) {
            return Response::json(['error' => 1,'msg'=> $e->getMessage()]);
        }

        Rmt::success('Berhasil menyimpan data');
        return Response::json(['error' => 0, 'msg' => '']);
    }   

    public function nilai(Request $r)
    {

        $mhs = Mahasiswareg::find($r->id_mhs_reg); 

        $ujian = DB::table('ujian_akhir')
                        ->where('id_mhs_reg', $r->id_mhs_reg)
                        ->where('jenis', Session::get('ua_jenis'))
                        ->first();

        $judul = !empty($ujian) ? $ujian->judul_tmp : '';

        $ketua = Sia::penguji($r->id_mhs_reg, Session::get('ua_jenis'), 'KETUA');
        $sekretaris = Sia::penguji($r->id_mhs_reg, Session::get('ua_jenis'), 'SEKRETARIS');
        $anggota = Sia::penguji($r->id_mhs_reg, Session::get('ua_jenis'), 'ANGGOTA');
        $anggota2 = Sia::penguji($r->id_mhs_reg, Session::get('ua_jenis'), 'ANGGOTA2');

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

                <!-- S2 -->
                <?php if ( Session::get('ua_prodi') == '61101' ) { ?>
                    <?php $anggota2 = Sia::penguji($r->id_mhs_reg, Session::get('ua_jenis'), 'ANGGOTA2'); ?>
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

                <!-- S1 -->
                <?php } else { ?>

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
                                nil_anggota = $('#nil_anggota').val();

                            $('#nil_ketua').blur(function(){
                                nil_ketua = $(this).val();

                                $('#total').nilai({
                                    nil_ketua : nil_ketua,
                                    nil_sekretaris : nil_sekretaris,
                                    nil_anggota : nil_anggota,
                                });
                            });

                            $('#nil_sekretaris').blur(function(){
                                nil_sekretaris = $(this).val();
                                
                                $('#total').nilai({
                                    nil_ketua : nil_ketua,
                                    nil_sekretaris : nil_sekretaris,
                                    nil_anggota : nil_anggota,
                                });
                            });

                            $('#nil_anggota').blur(function(){
                                nil_anggota = $(this).val();
                                
                                $('#total').nilai({
                                    nil_ketua : nil_ketua,
                                    nil_sekretaris : nil_sekretaris,
                                    nil_anggota : nil_anggota,
                                });
                            });
                            
                        });

                    </script>

                <?php } ?>

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
        $ketua = Sia::penguji($id_mhs_reg, $jenis, 'KETUA');
        $sekretaris = Sia::penguji($id_mhs_reg, $jenis, 'SEKRETARIS');
        $anggota = Sia::penguji($id_mhs_reg, $jenis, 'ANGGOTA');
        $anggota2 = Sia::penguji($id_mhs_reg, $jenis, 'ANGGOTA2');

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
        if ( $r->skripsi ) {
            
            try {

                return $this->nilaiStoreSkripsi($r);

            } catch( \Exception $e) {
                return Response::json(['error' => 1,'msg'=> $e->getMessage()]);
                // return Response::json(['error' => 1,'msg'=> $r->nil_anggota2]);
            }
        
        } else {

            try {

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

                DB::transaction(function()use($r,$nilai_huruf,$skala_nil, $nilai_akhir){

                    if ( Session::get('ua_prodi') <> 61101 ) {
                        $penguji = [
                            'KETUA' => $r->nil_ketua,
                            'SEKRETARIS' => $r->nil_sekretaris,
                            'ANGGOTA' => $r->nil_anggota
                        ];
                        $nilai = number_format(($r->nil_ketua + $r->nil_sekretaris + $r->nil_anggota) / 3,2);
                    } else {
                        $nilai = number_format(($r->nil_ketua + $r->nil_sekretaris + $r->nil_anggota + $r->nil_anggota2) / 3,2);
                        $penguji = [
                            'KETUA' => $r->nil_ketua, 
                            'SEKRETARIS' => $r->nil_sekretaris, 
                            'ANGGOTA' => $r->nil_anggota, 
                            'ANGGOTA2' => $r->nil_anggota2
                        ];
                    }

                    
                    foreach( $penguji as $key => $val ) {
                        DB::table('penguji')
                            ->where('id_mhs_reg', $r->id_mhs_reg)
                            ->where('jabatan', $key)
                            ->where('jenis', Session::get('ua_jenis'))
                            ->update(['nilai' => $val]);
                            // $tes[] = $key;
                    }


                    // Update nilai
                    // Cek apakah ada krsan untuk proposal/hasil
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

                            // Jika proposal simpan nilai pada field nilai_final
                            DB::table('nilai')
                                ->where('id_jdk', $r->id_jdk)
                                ->where('id_mhs_reg', $r->id_mhs_reg)
                                ->update(['nil_final' => $nilai_akhir]);
                        }
                    }
                });

            } catch(\Exception $e) {
                return Response::json(['error' => 1,'msg'=> $e->getMessage()]);
            }

            Rmt::success('Berhasil menyimpan data');
            return Response::json(['error' => 0, 'msg' => '']);

        }

    }

    private function nilaiStoreSkripsi($r)
    {

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

        DB::transaction(function()use($r,$nilai_huruf,$skala_nil){

            if ( Session::get('ua_prodi') <> 61101 ) {
                
                $penguji = [
                    'KETUA' => $r->nil_ketua,
                    'SEKRETARIS' => $r->nil_sekretaris,
                    'ANGGOTA' => $r->nil_anggota
                ];
                $nilai = number_format(($r->nil_ketua + $r->nil_sekretaris + $r->nil_anggota) / 3,2);

            } else {

                $nilai = number_format(($r->nil_ketua + $r->nil_sekretaris + $r->nil_anggota + $r->nil_anggota2) / 3,2);
                $penguji = [
                    'KETUA' => $r->nil_ketua, 
                    'SEKRETARIS' => $r->nil_sekretaris, 
                    'ANGGOTA' => $r->nil_anggota, 
                    'ANGGOTA2' => $r->nil_anggota2
                ];
            }

            foreach( $penguji as $key => $val ) {
                DB::table('penguji')
                    ->where('id_mhs_reg', $r->id_mhs_reg)
                    ->where('jabatan', $key)
                    ->where('jenis', Session::get('ua_jenis'))
                    ->update(['nilai' => $val]);
            }

            // Update nilai
            DB::table('nilai')
                ->where('id_jdk', $r->id_jdk)
                ->where('id_mhs_reg', $r->id_mhs_reg)
                ->update(['nilai_huruf' => $nilai_huruf, 'nilai_indeks' => $skala_nil->nilai_indeks]);

        });

        Rmt::success('Berhasil menyimpan data');
        return Response::json(['error' => 0, 'msg' => '']);

    }

    public function beritaAcara(Request $r)
    {
        $jenis = Session::get('ua_jenis');

        $data['mhs'] = Sia::mahasiswa()
                ->leftJoin('konsentrasi as k', 'm2.id_konsentrasi', 'k.id_konsentrasi')
                ->select('m2.nim','m1.nm_mhs','m2.id_prodi','p.nm_prodi','p.jenjang',
                        'm2.bebas_pembayaran','m2.jurnal_file','m2.jurnal_approved','m2.semester_mulai',
                        'k.nm_konsentrasi as konsentrasi')
                ->where('m2.id', $r->id_mhs_reg)
                ->first();

        $data['skripsi'] = DB::table('ujian_akhir')
                            ->where('id_mhs_reg', $r->id_mhs_reg)
                            ->where('jenis', $r->jenis)
                            ->first();

        $data['ketua'] = Sia::penguji($r->id_mhs_reg, $r->jenis, 'KETUA');
        $data['sekretaris'] = Sia::penguji($r->id_mhs_reg, $r->jenis, 'SEKRETARIS');
        $data['penguji'] = DB::table('penguji as p')
                        ->leftJoin('dosen as d', 'p.id_dosen', 'd.id')
                        ->select(DB::raw('concat_ws(\' \',d.gelar_depan,d.nm_dosen,d.gelar_belakang) as penguji'),'p.jabatan')
                        ->where('p.id_mhs_reg', $r->id_mhs_reg)
                        ->where('p.jenis', $r->jenis)
                        ->orderBy('p.id')
                        ->get();

        $canAksi = [];
        if ( empty($data['mhs']->jurnal_approved) ) {
            $canAksi[] = 'Belum setor jurnal';
        }

        if ( $data['mhs']->bebas_pembayaran == '0' ) {
            $canAksi[] = 'Pembayaran mahasiswa belum lunas';
        }

        if ( !empty($r->cetak) && $jenis == 'S' && $data['mhs']->semester_mulai > 20171 ) {
            if ( count($canAksi) > 0 ) {
                echo '<center><h3>Data Ujian Mahasiswa ini belum bisa dicetak karena: <br><b>'.implode(', ', $canAksi).'</b></h3></center>';
                exit;
            }
        }

        // jika $r->cetak kosong berarti s1
        switch ($r->cetak) {
            case 'undangan':
                return view('ujian-akhir.s2.undangan-seminar', $data);
            break;
            case 'daftar-hadir-penguji':
                return view('ujian-akhir.s2.print-daftar-hadir-penguji', $data);
            break;
            case 'daftar-hadir-ujian':
                return view('ujian-akhir.s2.print-daftar-hadir-ujian', $data);
            break;
            case 'nilai-ujian':
                return view('ujian-akhir.s2.print-nilai-ujian', $data);
            break;
            case 'saran':
                return view('ujian-akhir.s2.print-saran-perbaikan', $data);
            break;
            case 'berita-acara':
                return view('ujian-akhir.s2.print-berita-acara', $data);
            break;
            case 'rekapitulasi':
                return view('ujian-akhir.s2.print-rekapitulasi-nilai', $data);
            break;
            
            default:
                // S1
                if ( $jenis == 'S' ) {
                    return view('ujian-akhir.print-berita-acara-skripsi', $data);
                } else {
                    return view('ujian-akhir.print-berita-acara-seminar', $data);
                }
            break;
        }
    }

    public function rekapNilai(Request $r)
    {
        $mhs = DB::table('nilai as n')
                ->rightJoin('ujian_akhir as ua', 'ua.id_mhs_reg', 'n.id_mhs_reg')
                ->leftJoin('jadwal_kuliah as jdk', 'n.id_jdk', 'jdk.id')
                ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
                ->leftJoin('matakuliah as mk', 'mkur.id_mk','=','mk.id')
                ->select('ua.tgl_ujian')
                ->where('mk.ujian_akhir', Session::get('ua_jenis'))
                ->where('jdk.id_prodi', Session::get('ua_prodi'))
                ->where('jdk.id_smt', Session::get('ua_semester'))
                ->orderBy('ua.tgl_ujian')
                ->get(); ?>

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
}
