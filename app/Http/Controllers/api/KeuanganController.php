<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Rmt, Response, Carbon, Session, DB, Mail;
use App\Briva;
use App\Mahasiswareg, App\Pembayaran;

class KeuanganController extends Controller
{

    private $bri_key;
    private $otorisasi;
    private $app_id;
    private $app_secret;
    private $institutionCode;
    private $brivaNo;
    private $restApiUrl;

    use Library;

    public function __construct(Request $r)
    {
        $this->bri_key = env('bri_key');
        $this->otorisasi = env('otorisasi');
        $this->app_id = env('app_id');
        $this->app_secret = env('app_secret');
        $this->institutionCode = env('institutionCode');
        $this->brivaNo = env('brivaNo');
        $this->restApiUrl = env('restApiUrl');

        Rmt::auth(config('app.token'), $r->token);
    }

    public function index(Request $r)
    {

        $mhs = Mahasiswareg::where('nim', $r->nim)->first();

        $periode = Rmt::periodeBerjalan($mhs->id_prodi);
        $ta_aktif = empty($r->ta) || $r->ta == 'null' ? $periode : $r->ta;

        $data = $this->mhsHistory($mhs->id, $ta_aktif, $mhs->semester_mulai, $mhs->id_prodi);

        if ( !empty($data['tagihan']) ) {
            $semester = DB::table('semester')
                        ->whereBetween('id_smt', [$mhs->semester_mulai, $ta_aktif])
                        ->orderBy('id_smt','desc')->get();

            $potongan = $this->totalPotonganPerMhs($mhs->id, $mhs->semester_mulai, $ta_aktif);

            $total_bayar = 0;
            $sisa_bayar = 0;
            $total_tagihan = 0;
            $all_potongan = [];
            $history_bayar = [];

            if ( $this->posisiSemesterMhs($mhs->semester_mulai, $ta_aktif) > 1 ) {
                
                $total_tagihan = $data['tagihan']->bpp;

            } else {

                $total_tagihan = $data['tagihan']->bpp + $data['tagihan']->spp + $data['tagihan']->seragam + $data['tagihan']->lainnya;
            }

            $total_tagihan = $total_tagihan - $potongan;

            $tagihan = [
                [
                    'nama' => 'BPP',
                    'jml' => 'Rp '.Rmt::rupiah($data['tagihan']->bpp)
                ],
                [
                    'nama' => 'SPP',
                    'jml' => 'Rp '.Rmt::rupiah($data['tagihan']->spp),
                ],
                [
                    'nama' => 'Seragam',
                    'jml' => 'Rp '.Rmt::rupiah($data['tagihan']->seragam),
                ],
                [
                    'nama' => 'Lain-lain',
                    'jml' => empty($data['tagihan']->lainnya) ? '0' : 'Rp '.Rmt::rupiah($data['tagihan']->lainnya),
                ]
            ];

            if ( !empty($potongan) ) {
                    
                $potong = \App\PotonganBiayaKuliah::where('id_mhs_reg', $mhs->id)->get();

                foreach( $potong as $po ){
                    $ket = empty($po->ket) || $po->ket == '-' ? '':' ('.$po->ket.')';
                    $all_potongan[] = [
                        'nama' => $po->jenis_potongan.$ket,
                        'jml' => 'Rp '.Rmt::rupiah($po->potongan)
                    ];
                }
            }


            $loop = 1;
            foreach( $data['pembayaran'] as $pmb ) {
                $history_bayar[] = [
                    'no' => $loop++,
                    'tgl_bayar' => Carbon::parse($pmb->tgl_bayar)->format('d/m/Y'),
                    'ket' => $pmb->ket,
                    'jenis_bayar' => $pmb->jenis_bayar,
                    'jml' => Rmt::rupiah($pmb->jml_bayar)
                ];

                $total_bayar += $pmb->jml_bayar;
            }

            $sisa_bayar = $total_tagihan - $total_bayar;

        } else {
            $total_tagihan = 0;
            $tagihan    = [];
            $all_potongan   = [];
            $history_bayar  = [];
            $total_bayar    = 0;
            $sisa_bayar = 0;
        }

        $total_tagihan = empty($total_tagihan) ? '0' : 'Rp '.Rmt::rupiah($total_tagihan);

               $result_ta =  $this->semester($mhs->semester_mulai, $periode, $ta_aktif);
        
        // Tunggakan
            $smt_mulai = $mhs->semester_mulai > Rmt::smtMulaiOnTunggakan() ? $mhs->semester_mulai : Rmt::smtMulaiOnTunggakan();

            $jml_smt = Rmt::smtTunggakanNum($mhs->id, $smt_mulai, $periode);

            $tunggakan = $this->tunggakan($mhs->id, $smt_mulai, $periode, $jml_smt);

        // Menunggu pembayaran
            $briva = $this->menungguPembayaran($mhs->nim);

        // $data_mhs = [
        //     'nim' => $mhs->nim,
        //     'nama' => $mhs->mhs->nm_mhs,
        //     'smstr' => $this->posisiSemesterMhs($mhs->semester_mulai, $ta_aktif)
        // ];

        // History transaksi
        $transaksiArr = [];
        $transaksi = Briva::where('nim', $mhs->nim)
                ->where('status', 'Y')
                ->orderBy('id','desc')
                ->take(5)
                ->get();
        foreach( $transaksi as $tr ) {
            if ( $tr->jenis_bayar == 88 ) {
                $jenis = 'Tunggakan';
            } elseif ( $tr->jenis_bayar == 99 ) {
                $jenis = 'Semester Pendek';
            } elseif ( $tr->jenis_bayar == 0 ) {
                $jenis = 'Biaya Kuliah';
            } else {
                $jenis = $tr->jenisBayar->ket;
            }

            $transaksiArr[] = [
                'tgl_bayar' => Carbon::parse($tr->updated_at)->format('d/m/Y'),
                'jenis' => $jenis,
                'ket' => $tr->ket,
                'jml' => 'Rp '.Rmt::rupiah($tr->jml)
            ];
        }

        $data = [
            'ta' => $result_ta,
            'ta_aktif' => $ta_aktif,
            'ta_berjalan' => $periode,
            // 'mhs' => $data_mhs,
            'total_tagihan' => $total_tagihan,
            'tagihan' => $tagihan,
            'all_potongan' => $all_potongan,
            'history_bayar' => $history_bayar,
            'total_bayar' => !empty($total_bayar) ? 'Rp '.Rmt::rupiah($total_bayar):0,
            'sisa_bayar' => !empty($sisa_bayar) ? 'Rp '.Rmt::rupiah($sisa_bayar):0,
            'tunggakan' => !empty($tunggakan) ? 'Rp '.Rmt::rupiah($tunggakan) : 0,
            'briva' => $briva,
            'transaksi' => $transaksiArr
        ];

        $result = ['error' => 0, 'data' => $data];
        // dd($result);
        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function history(Request $r)
    {
        $transaksiArr = [];
        $transaksi = Briva::where('nim', $r->nim)
                ->where('status', 'Y')
                ->orderBy('id','desc')
                ->get();
        foreach( $transaksi as $tr ) {
            if ( $tr->jenis_bayar == 88 ) {
                $jenis = 'Tunggakan';
            } elseif ( $tr->jenis_bayar == 99 ) {
                $jenis = 'Semester Pendek';
            } elseif ( $tr->jenis_bayar == 0 ) {
                $jenis = 'Biaya Kuliah';
            } else {
                $jenis = $tr->jenisBayar->ket;
            }

            $transaksiArr[] = [
                'tgl_bayar' => Carbon::parse($tr->updated_at)->format('d/m/Y'),
                'jenis' => $jenis,
                'ket' => $tr->ket,
                'jml' => 'Rp '.Rmt::rupiah($tr->jml)
            ];
        }

        $result = ['error' => 0, 'data' => $transaksiArr];
        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }

    private function menungguPembayaran($nim)
    {
        $today = Carbon::now();
        $bri = Briva::where('nim', $nim)
            ->where('status', 'N')
            ->where('exp_date', '>', $today)
            ->orderBy('id','desc')
            ->first();

        $data_briva = [];
        if ( count($bri) > 0 ) {
            if ( $bri->jenis_bayar == 0 ) {
                $jenis = 'Biaya Kuliah';
            } elseif ( $bri->jenis_bayar == 88 ) {
                $jenis = 'Tunggakan';
            } elseif ( $bri->jenis_bayar == 99 ) {
                $jenis = 'Semester Pendek';
            } else {
                $getJns = DB::table('jenis_pembayaran')
                            ->where('id_jns_pembayaran', $bri->jenis_bayar)
                            ->first();

                $jenis = $getJns->ket;
            }

            $data_briva = [
                'id' => $bri->id,
                'jenis' => $jenis,
                'jml' => 'Rp '.Rmt::rupiah($bri->jml),
                'exp_date' => Rmt::tgl_indo($bri->exp_date).' '.$bri->exp_date->format('H:i').' WITA',
                'cust_code' => $this->brivaNo."-".$bri->cust_code
            ];
        }

        return $data_briva;
    }

    private function mhsHistory($id, $smt, $semester_mulai, $id_prodi)
    {

        $data['pembayaran'] = $this->historyBayar($smt)
                                ->where('p.id_mhs_reg', $id)
                                ->orderBy('p.tgl_bayar')->get();

        $data['tagihan'] = DB::table('biaya_kuliah')
                    ->where('tahun', substr($semester_mulai,0,4))
                    ->where('id_prodi', $id_prodi)
                    ->first();

        return $data;
    }

    public function cekPembayaran($id_briva = null)
    {
        try {

            $briva = $this->getBriva($id_briva);

            $terbayar = 0;

            $mhs = [];

            // Cek status pembayaran
            if ( count($briva) > 0 ) {

                foreach( $briva as $bri ) {
                    $status = $this->cekPembayaranBriva($bri->cust_code);

                    if ( $status && $status->data->statusBayar == 'Y' ) {
                        DB::transaction(function()use($bri) {
                            
                            $data = Briva::find($bri->id);
                            $data->status = 'Y';
                            $data->save();

                            $this->insertPembayaran($bri);


                        });

                        $mhs[] = ['id' => $bri->id, 'nim' => $bri->nim];
                        $terbayar += 1;

                    }
                }

            }

            return Response::json(['error' => 0, 'diekseskui' => count($briva), 'terbayar' => $terbayar, 'mhs' => $mhs]);

        } catch( \Exception $e ) {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()]);
        }
    }

    private function getBriva($id_briva = null)
    {
        $today = Carbon::now();
        $data = Briva::where('status', 'N')
                ->where('exp_date', '>', $today);

        if ( !empty($id_briva) ) {
            $data->where('id', $id_briva);
        }

        $briva = $data->orderBy('id','desc')
                    ->take(20)
                    ->get();

        return $briva;
    }

    private function cekPembayaranBriva($custCode)
    {

        if ( $this->cekToken() ) {

            $param = '/status/'.$this->institutionCode.'/'.$this->brivaNo.'/'.$custCode;
            $header = ['Content-Type: application/json'];
            $token = Session::get('token');
            $create = $this->curl([], $token, 'get', $header, $param);
            $status = json_decode($create);

            if ( !empty($status) && $status->status ) {
                return $status;
            } else {
                return false;
            }

        } else {
            return false;
        }
    }

    private function insertPembayaran($data)
    {
        if ( $data->jenis_bayar == 88 ) {

            $this->insertTunggakan($data, Rmt::smtMulaiOnTunggakan());
        
        } else {

            $cek = Pembayaran::where('id_mhs_reg', $data->id_mhs_reg)
                    ->where('id_smt', $data->id_smt)
                    ->where('tgl_bayar', Carbon::parse($data->updated_at)->format('Y-m-d'))
                    ->where('jml_bayar', $data->jml)
                    ->where('id_jns_pembayaran', $data->jenis_bayar)
                    ->where('ket', $data->ket)
                    ->count();

            if ( $cek == 0 ) {
                $byr = new Pembayaran;
                $byr->id_smt = $data->id_smt;
                $byr->id_mhs_reg = $data->id_mhs_reg;
                $byr->tgl_bayar = Carbon::parse($data->updated_at)->format('Y-m-d');
                $byr->jml_bayar = $data->jml;
                $byr->id_bank = 'B';
                $byr->jenis_bayar = 'BRIVA';
                $byr->ket = $data->ket;
                $byr->id_jns_pembayaran = $data->jenis_bayar;
                $byr->save();

                if ( $data->jenis_bayar == 0 ) {
                    $krs = \App\KrsStatus::where('id_mhs_reg', $data->id_mhs_reg)
                            ->where('id_smt', $data->id_smt)
                            ->count();

                    if ( $krs == 0 ) {
                        $data_krs = new \App\KrsStatus;
                        $data_krs->id_mhs_reg = $data->id_mhs_reg;
                        $data_krs->id_smt = $data->id_smt;
                        $data_krs->save();
                    }

                    // Insert kartu ujian
                    $this->insertKartuUjian($data->id_mhs_reg, $data->id_smt);
                }
            }
        }
    }

    private function insertTunggakan($data, $smt_mulai_nuggak)
    {
        $mhs = DB::table('mahasiswa_reg')
                ->where('id', $data->id_mhs_reg)
                ->first();

        $smt_mulai = $mhs->semester_mulai > $smt_mulai_nuggak ? $mhs->semester_mulai : $smt_mulai_nuggak;
        $jml_smt = Rmt::smtTunggakanNum($mhs->id, $smt_mulai, Rmt::periodeBerjalan($mhs->id_prodi));

        $list_smt = Rmt::listSmtTunggakan($smt_mulai_nuggak, $jml_smt);

        $dibayar = $data->jml;

        foreach( $list_smt as $key => $val ) {

            $tunggakan = Rmt::tunggakan($mhs->id, $val, $val, $jml_smt = 1);

            if ( $tunggakan == 0 ) continue;

            $saldo = $dibayar - $tunggakan;

            if ( $saldo > 0 ) {

                $this->storeTunggakan($data, $val, $tunggakan);

            } else {

                $this->storeTunggakan($data, $val, $dibayar);
                break;
            }
            $dibayar = $saldo;

        }
            
    }

    private function storeTunggakan($data, $id_smt, $jml)
    {
        $byr = new Pembayaran;
        $byr->id_smt = $id_smt;
        $byr->id_mhs_reg = $data->id_mhs_reg;
        $byr->tgl_bayar = Carbon::parse($data->updated_at)->format('Y-m-d');
        $byr->jml_bayar = $jml;
        $byr->id_bank = 'B';
        $byr->jenis_bayar = 'BRIVA';
        $byr->ket = $data->ket;
        $byr->id_jns_pembayaran = 0;
        $byr->save();
    }

    private function insertKartuUjian($id_mhs_reg, $id_smt)
    {
        $mhs = Mahasiswareg::find($id_mhs_reg);

        $biaya = Rmt::biayaPerMhs($id_mhs_reg, $mhs->semester_mulai, $id_smt, $mhs->id_prodi);
        $potongan = Rmt::totalPotonganPerMhs($id_mhs_reg, $mhs->semester_mulai, $id_smt);
        $tagihan = $biaya - $potongan;
        $total_bayar = Rmt::totalBayar($id_mhs_reg, $id_smt);
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

    /* Store Bayar */
        public function store(Request $r)
        {

            try {

                $mhs = Mahasiswareg::where('nim', $r->nim)->firstOrFail();

                // Validasi agar jumlah dibayar tidak lebih besar dari tagihan
                if ( $r->jenis_bayar == 0 || $r->jenis_bayar == 88 ) {
                    // Biaya Kuliah
                    if ( $r->jml_bayar_num > $r->tagihan ) {
                        return Response::json(['error' => 1, 'msg' => 'Jumlah yang dibayar lebih besar dari tagihan']);
                    }
                }

                $id_mhs_reg = $mhs->id;
                $nim = $mhs->nim;
                $nama = $mhs->mhs->nm_mhs;
                $cust_code = str_replace('MM', '00', $nim);
                $exp_date = Carbon::now()->addDays(30);

                $cek = Briva::where('cust_code', $cust_code)
                        ->where('status', 'N')
                        ->count();

                // Jika masih ada pembayaran yang belum selesai
                if ( $cek > 0 ) {
                    return Response::json(['error' => 1, 'msg' => 'Masih ada pembayaran yang belum selesai']);
                }

                $dataBriva = [
                    'nama' => $nama,
                    'custCode' => $cust_code,
                    'amount' => $r->jml,
                    'ket' => $r->ket,
                    'exp_date' => "$exp_date"
                ];


                if ( !$this->createBriva($dataBriva) ) {
                    return Response::json(['error' => 1, 'msg' => 'Server Gangguan, ulangi lagi beberapa saat kemudian.']);
                }

                DB::transaction(function()use($id_mhs_reg, $nim, $dataBriva, $r){
                    $data = new Briva;
                    $data->id_smt = $r->ta;
                    $data->id_mhs_reg = $id_mhs_reg;
                    $data->nim = $nim;
                    $data->cust_code = $dataBriva['custCode'];
                    $data->nama = $dataBriva['nama'];
                    $data->jml = $r->jml;
                    $data->ket = $r->ket;
                    $data->jenis_bayar = $r->jenis;
                    $data->exp_date = $dataBriva['exp_date'];
                    $data->save();
                });
            }
            catch(\Exception $e)
            {
                return Response::json(['error' => 1, 'msg' => $e->getMessage()]);
            }

            return Response::json(['error' => 0, 'msg' => '']);
        }

    /* Batalkan */
        public function delete(Request $r)
        {

            try {
                $briva = Briva::where('id', $r->id)
                    ->where('status', 'N')
                    ->firstOrFail();
                $mhs = Mahasiswareg::where('id', $briva->id_mhs_reg)->firstOrFail();

                if ( !empty($briva) ) {

                    $status = $this->cekPembayaranBriva($briva->cust_code);

                    // Jika status pada web service 'N' maka do delete
                    if ( $status && $status->data->statusBayar == 'N' ) {

                        $delete = $this->deleteBriva($briva->cust_code);

                        if ( $delete && $delete->status ) {
                            Briva::where('id', $r->id)->delete();
                        } else {
                            return Response::json(['error' => 1, 'msg' => 'Terjadi gangguan, mohon ulangi lagi']);
                        }

                    } else {
                        return Response::json(['error' => 1, 'msg' => 'Mohon ulangi lagi atau mungkin Pembayaran ini telah selesai, tunggu hingga pembayaran anda divalidasi oleh sistem.']);
                    }

                } else {

                    return Response::json(['error' => 1, 'msg' => 'Pembayaran ini telah selesai']);
                }

            } catch( \Exception $e ) {
                return Response::json(['error' => 1, 'msg' => 'Terjadi gangguan, mohon ulangi lagi']);
            }

            return Response::json(['error' => 0, 'msg' => 'Pembayaran telah dibatalkan']);
        }

    /* Jenis-jeni pembayaran */
        public function jenis(Request $r)
        {
            $mhs = Mahasiswareg::where('nim', $r->nim)->firstOrFail();

            $jenis_bayar = Rmt::jenisBayar($mhs->id_prodi);

            $data[] = [ 'id' => '0', 'ket' => 'Biaya Kuliah'];

            foreach( $jenis_bayar as $jb )
            {
                $data[] = [ 'id' => $jb->id_jns_pembayaran, 'ket' => $jb->ket];
            }

            $data[] = [ 'id' => '99', 'ket' => 'Semester Pendek'];
            $data[] = [ 'id' => '88', 'ket' => 'BAYAR TUNGGAKAN'];

            $result = ['error' => 0, 'data' => $data];

            return Response::json($result, 200);
        }

    public function mailPembayaranReminder($nim)
    {
        if ( empty($nim) ) { 
            return Response::json(['error' => 1, 'msg' => 'Under Maintenance'], 200);
        }

        $bri = Briva::where('nim', $nim)
                ->where('status', 'N')->firstOrFail();

        $mhs = DB::table('mahasiswa_reg as m1')
                ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                ->select('m2.email','m2.nm_mhs')
                ->where('m1.nim', $nim)
                ->first();

        $mail_chek = explode('@', $mhs->email);
        if ( trim($mail_chek[0]) == trim($nim) ) {
            return Response::json(['error' => 1, 'msg' => 'Email tidak valid']);
        }

        if ( $bri->jenis_bayar == 88 ) {
            $jenis = 'Tunggakan';
        } elseif ( $bri->jenis_bayar == 99 ) {
            $jenis = 'Semester Pendek';
        } elseif ( $bri->jenis_bayar == 0 ) {
            $jenis = 'Biaya Kuliah';
        } else {
            $jenis = $bri->jenisBayar->ket;
        }

        $mail['subjek']    = 'Menunggu Pembayaran BRI Virtual Account untuk Pembayaran '.$jenis;
        $mail['email']     = $mhs->email;
        // $mail['email']     = 'abd.rahmat.ika@gmail.com';

        $data['header'] = 'Hai '.$mhs->nm_mhs.',';
        $data['msg'] = 'Permintaan pembayaran '.$jenis.' yang diterima pada '.Carbon::parse($bri->created_at)->format('d/m/Y H:i') .' WITA berhasil dibuat. Mohon segera selesaikan pembayaran sebelum batas waktu pembayaran berakhir.';
        $data['cust_code'] = $this->brivaNo.$bri->cust_code;
        $data['amount'] = 'Rp '.Rmt::rupiah($bri->jml);
        $data['exp_date'] = $bri->exp_date->format('d/m/Y H:i').' WITA';

        try {
            Mail::send('email.pembayaran-reminder', $data, function ($message)use($mail)
            {
                $message->from('nobel@stienobel-indonesia.ac.id', 'STIE Nobel Indonesia');
                $message->to($mail['email']);
                $message->subject($mail['subjek']);
            });
        }
        catch ( \Exception $e )
        {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()], 200);
        }

        return Response::json(['error' => 0, 'msg' => 'Sukses'], 200);
        // return view('email.pembayaran-reminder', $data);
    }

    public function mailPembayaranSukses($nim, $id)
    {
        if ( empty($nim) ) {
            return Response::json(['error' => 1, 'msg' => 'Under Maintenance'], 200);
        }

        $bri = DB::table('briva as bri')
                ->leftJoin('mahasiswa_reg as m1', 'bri.id_mhs_reg', 'm1.id')
                ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                ->select('bri.*','m1.nim','m2.nm_mhs', 'm2.email')
                ->where('bri.id', $id)
                ->where('bri.nim', $nim)
                ->first();

        if ( empty($bri->email) ) {
            return Response::json(['error' => 1, 'msg' => 'Empty email'], 200);
        }

        if ( empty($bri) ) {
            return Response::json(['error' => 1, 'msg' => 'Data Briva tidak ditemukan'], 200);
        }

        $mail_chek = explode('@', $bri->email);
        if ( trim($mail_chek[0]) == trim($nim) ) {
            return Response::json(['error' => 1, 'msg' => 'Email tidak valid']);
        }

        if ( $bri->jenis_bayar == 88 ) {
            $jenis = 'Tunggakan';
        } elseif ( $bri->jenis_bayar == 99 ) {
            $jenis = 'Semester Pendek';
        } elseif ( $bri->jenis_bayar == 0 ) {
            $jenis = 'Biaya Kuliah';
        } else {
            $jns = DB::table('jenis_pembayaran')
                    ->where('id_jns_pembayaran', $bri->jenis_bayar)
                    ->select('ket')
                    ->first();

            $jenis = $jns->ket;
        }

        $mail['subjek']    = 'Pembayaran '.$jenis.' Anda Melalui BRI Virtual Account Berhasil';
        // $mail['email']     = 'abd.rahmat.ika@gmail.com';
        $mail['email']     = $bri->email;

        $data['header'] = 'Selamat, Pembayaran '.$jenis.' Anda Berhasil';
        $data['msg'] = [
            'nama' => $bri->nm_mhs,
            'nim' => $bri->nim,
            'amount' => 'Rp '.Rmt::rupiah($bri->jml),
            'tgl_bayar' => Carbon::parse($bri->updated_at)->format('d/m/Y H:i'). ' WITA',
            'jenis_bayar' => $jenis
        ];

        try {
            Mail::send('email.pembayaran-sukses', $data, function ($message)use($mail)
            {
                $message->from('nobel@stienobel-indonesia.ac.id', 'STIE Nobel Indonesia');
                $message->to($mail['email']);
                $message->subject($mail['subjek']);
                $message->cc('keuangan@stienobel-indonesia.ac.id');
            });
        }
        catch ( \Exception $e )
        {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()], 200);
        }

        return Response::json(['error' => 0, 'msg' => 'Sukses'], 200);
        // return view('email.pembayaran-sukses', $data);
    }

    public function mailPembayaranBatal($nim)
    {
        if ( empty($nim) ) {
            return Response::json(['error' => 1, 'msg' => 'Under Maintenance'], 200);
        }

        try {

            $bri = Briva::where('nim', $nim)->firstOrFail();

            $mhs = DB::table('mahasiswa_reg as m1')
                    ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->select('m2.email','m2.nm_mhs')
                    ->where('m1.nim', $nim)
                    ->first();

            if ( empty($bri) ) {
                return Response::json(['error' => 1, 'msg' => 'Data Briva tidak ditemukan'], 200);
            }

            $mail_chek = explode('@', $mhs->email);
            if ( trim($mail_chek[0]) == trim($nim) ) {
                return Response::json(['error' => 1, 'msg' => 'Email tidak valid']);
            }

            if ( $bri->jenis_bayar == 88 ) {
                $jenis = 'Tunggakan';
            } elseif ( $bri->jenis_bayar == 99 ) {
                $jenis = 'Semester Pendek';
            } elseif ( $bri->jenis_bayar == 0 ) {
                $jenis = 'Biaya Kuliah';
            } else {
                $jns = DB::table('jenis_pembayaran')
                        ->where('id_jns_pembayaran', $bri->jenis_bayar)
                        ->select('ket')
                        ->first();

                $jenis = $jns->ket;
            }

            $mail['subjek']    = 'Pembayaran '.$jenis.' Anda telah kami batalkan';
            // $mail['email']     = 'abd.rahmat.ika@gmail.com';
            $mail['email']     = $mhs->email;

            $data['header'] = 'Hai '.$mhs->nm_mhs.', Pembayaran melalui BRI Virtual Account dibatalkan';
            $data['msg'] = 'Pembayaran melalui BRI Virtual Account untuk pembayaran '.$jenis.' telah kami batalkan';
            $data['cust_code'] = $this->brivaNo.$bri->cust_code;
            $data['amount'] = 'Rp '.Rmt::rupiah($bri->jml);
            $data['exp_date'] = $bri->exp_date->format('d/m/Y H:i').' WITA';

            Mail::send('email.pembayaran-batal', $data, function ($message)use($mail)
            {
                $message->from('nobel@stienobel-indonesia.ac.id', 'STIE Nobel Indonesia');
                $message->to($mail['email']);
                $message->subject($mail['subjek']);
                $message->cc('kabag@stienobel-indonesia.ac.id');
            });
        }
        catch ( \Exception $e )
        {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()], 200);
        }

        return Response::json(['error' => 0, 'msg' => 'Sukses'], 200);
        // return view('email.pembayaran-batal', $data);
    }

    /* Web service engine */
        private function createBriva($param)
        {

            $data = array(
                'institutionCode' => $this->institutionCode,
                'brivaNo'=> $this->brivaNo,
                'custCode'=> $param['custCode'],
                'nama'=> $param['nama'],
                'amount'=> $param['amount'],
                'keterangan'=> $param['ket'],
                'expiredDate'=> $param['exp_date']
            );

            if ( $this->cekToken() ) {

                $token = Session::get('token');
                $create = $this->curl($data, $token, 'post');
                $create_data = json_decode($create);

                if ( !$create_data->status ) {

                    if ( $create_data->responseCode == '13' ) {

                        return $this->updateBriva($param);

                    } else {

                        $this->destroyToken();
                        return false;
                    }
                    
                } else {
                    return true;
                }

            } else {
                return false;
            }
        }

        private function updateStatus($custCode)
        {
            $data = array(
                'institutionCode' => $this->institutionCode,
                'brivaNo'=> $this->brivaNo,
                'custCode'=> $custCode,
                'statusBayar' => 'N'
            );

            if ( $this->cekToken() ) {

                $param = '/status';
                $header = ['Content-Type: application/json'];
                $token = Session::get('token');
                $update = $this->curl($data, $token, 'put', $header, $param);
                $updated_data = json_decode($update);

                if ( !$updated_data->status ) {

                    $this->destroyToken();
                    return false;
                    
                } else {
                    return true;
                }

            } else {
                return false;
            }
        }

        private function updateBriva($param)
        {

            $data = array(
                'institutionCode' => $this->institutionCode,
                'brivaNo'=> $this->brivaNo,
                'custCode'=> $param['custCode'],
                'nama'=> $param['nama'],
                'amount'=> $param['amount'],
                'keterangan'=> $param['ket'],
                'expiredDate'=> $param['exp_date']
            );

            if ( $this->cekToken() ) {

                $token = Session::get('token');
                $update = $this->curl($data, $token, 'put');
                $updated_data = json_decode($update);

                if ( !$updated_data->status ) {

                    $this->destroyToken();
                    return false;
                    
                } else {
                    if ( $this->updateStatus($param['custCode']) ) {
                        return true;
                    } else {
                        return false;
                    }
                }

            } else {
                return false;
            }
        }

        public function deleteAkunBriva($code)
        {
            $del = $this->deleteBriva($code);

            return $del;
        }

        private function deleteBriva($custCode)
        {

            $data = "institutionCode=".$this->institutionCode."&brivaNo=".$this->brivaNo."&custCode=".$custCode;

            if ( $this->cekToken() ) {
                $token = Session::get('token');
                $delete = $this->curlDelete($data, $token);
                $deleted_data = json_decode($delete);

                if ( !$deleted_data->status ) {

                    $this->destroyToken();
                    return false;
                    
                } else {
                    return $deleted_data;
                }

            } else {
                return false;
            }
        }

        public function report($tgl)
        {
            $data = $this->reportBriva($tgl);
        }

        private function reportBriva($tgl)
        {

            if ( $this->cekToken() ) {
                $header = ["Content-Type: application/json"];
                $param = "/report/$this->institutionCode/$this->brivaNo/$tgl/$tgl";
                $token = Session::get('token');
                $result = $this->curl([], $token, 'delete', $header, $param);
                $result = json_decode($result);

                if ( !$result->status ) {

                    $this->destroyToken();
                    return false;
                    
                } else {
                    return $result;
                }

            } else {
                return false;
            }
        }

        private function destroyToken()
        {
            Session::pull('token');
            Session::pull('expired_token');
        }

        private function getToken()
        {

            try {
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $this->restApiUrl."token");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                curl_setopt($ch, CURLOPT_POST, TRUE);

                curl_setopt($ch, CURLOPT_POSTFIELDS, "{
                  \"grant_type\": \"authorization_code\",
                  \"client_id\": \"".$this->app_id."\",
                  \"client_secret\": \"".$this->app_secret."\",
                  \"code\": \"".$this->otorisasi."\"
                }");

                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                  "X-BRI-KEY: ".$this->bri_key,
                  "Content-Type: application/json"
                ));

                $response = curl_exec($ch);
                curl_close($ch);
                
                $token = json_decode($response);

                if ( $token->status ) {

                    Session::pull('token');
                    Session::put('token', $token->data->access_token);

                    Session::set('expired_token', Carbon::now()->addMinutes($token->data->expires_in/60));

                    return true;
                } else {
                    throw new \Exception($token->responseDescription, 1);
                }

            }
            catch( \Exception $e)
            {
                // return $e->getMessage();
                return false;
            }

        }

        private function cekToken()
        {
            if ( Session::has('expired_token') ) {
                // Jika token expired
                if ( Carbon::now() >= Session::get('expired_token') ) {
                    return $this->getToken();
                }
            } else {
                return $this->getToken();
            }

            return true;
        }

        private function curl($data, $token, $method, $header = ["Content-Type: application/json"], $param = '')
        {

            $fields = json_encode($data);

            $headers = array(
              "Authorization: Bearer ".$token,
              "X-BRI-KEY: ".$this->bri_key
            );

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $this->restApiUrl.'briva'.$param);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            if ( $method == 'post' ) {
                curl_setopt($ch, CURLOPT_POST, TRUE);
            } elseif ( $method == 'put' ) {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
            }

            if ( count($data) != 0 ) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            }
            

            curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($headers, $header));

            $response = curl_exec($ch);
            curl_close($ch);
            return $response;

        }

        private function curlDelete($data, $token)
        {

            $headers = array(
              "Authorization: Bearer ".$token,
              "X-BRI-KEY: ".$this->bri_key,
              "Content-Type: x-www-form-urlencoded"
            );

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $this->restApiUrl.'briva');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE"); 

            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            curl_close($ch);
            return $response;

        }

}
