<?php

namespace App\Http\Controllers\mahasiswa;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Sia, Rmt, DB, Response, Session, Carbon, Mail;
use App\Briva;
use App\Pembayaran;
use App\Mahasiswareg;

class KeuanganController extends Controller
{
    private $bri_key;
    private $otorisasi;
    private $app_id;
    private $app_secret;
    private $institutionCode;
    private $brivaNo;
    private $restApiUrl;

    public function __construct(Request $r)
    {
        $this->bri_key = env('bri_key');
        $this->otorisasi = env('otorisasi');
        $this->app_id = env('app_id');
        $this->app_secret = env('app_secret');
        $this->institutionCode = env('institutionCode');
        $this->brivaNo = env('brivaNo');
        $this->restApiUrl = env('restApiUrl');
    }

    public function index(Request $r)
    {
        $data = $this->mhsHistory($r, Sia::sessionMhs());

        $data['semester'] = DB::table('semester')
                            ->whereBetween('id_smt', [$data['mhs']->semester_mulai, Sia::sessionPeriode('berjalan')] )
                            ->orderBy('id_smt','desc')->get();

        $data['briva'] = $this->getBriva();

        // Cek status pembayaran
        if ( count($data['briva']) > 0 ) {
            $status = $this->cekPembayaran($data['briva'][0]->cust_code);

            if ( $status && $status->data->statusBayar == 'Y' ) {
                DB::transaction(function()use($data) {
                    $bri = Briva::find($data['briva'][0]->id);
                    $bri->status = 'Y';
                    $bri->save();

                    $this->insertPembayaran($data['briva'][0]);

                });

                Session::flash('mail-sukses', ['nim' => Sia::sessionMhs('nim'), 'id' => $data['briva'][0]->id ]);
                
                // after update, ambil ulang data di tabel briva
                $data['briva'] = $this->getBriva();
            }

        }

        $data['transaksi'] = Briva::where('nim', Sia::sessionMhs('nim'))
                ->where('status', 'Y')
                ->orderBy('id','desc')
                ->take(5)
                ->get();

        $this->deleteExpired();

        return view('mahasiswa-member.pembayaran.index', $data);
    }

    private function cekPembayaran($custCode)
    {

        if ( $this->cekToken() ) {

            $param = '/'.$this->institutionCode.'/'.$this->brivaNo.'/'.$custCode;
            $header = ['Content-Type: application/json'];
            $token = Session::get('token');
            $create = $this->curl([], $token, 'get', $header, $param);
            $status = json_decode($create);

            if ( !empty( $status ) && $status->status ) {
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

    private function insertTunggakan($data, $smt_mulai_nuggak)
    {
        $mhs = DB::table('mahasiswa_reg')
                ->where('id', $data->id_mhs_reg)
                ->first();

        $smt_mulai = $mhs->semester_mulai > $smt_mulai_nuggak ? $mhs->semester_mulai : $smt_mulai_nuggak;
        $jml_smt = Sia::smtTunggakanNum($mhs->id, $smt_mulai, Sia::sessionPeriode('berjalan'));

        $list_smt = Rmt::listSmtTunggakan($smt_mulai_nuggak, $jml_smt);

        $dibayar = $data->jml;

        foreach( $list_smt as $key => $val ) {

            $tunggakan = Sia::tunggakan($mhs->id, $val, $val, $jml_smt = 1);

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

        $biaya = Rmt::biayaPerMhs($id_mhs_reg, $mhs->semester_mulai, $id_smt);
        $potongan = Sia::totalPotonganPerMhs($id_mhs_reg, $mhs->semester_mulai, $id_smt);
        $tagihan = $biaya - $potongan;
        $total_bayar = Sia::totalBayar($id_mhs_reg, $id_smt);
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

    public function bayar(Request $r)
    {
        // if ( !$r->ajax() ) {
        //     dd('Server under maintenance. Please try again later.');
        // }

        try {

            // Validasi agar jumlah dibayar tidak lebih besar dari tagihan
            if ( $r->jenis_bayar == 0 || $r->jenis_bayar == 88 ) {
                // Biaya Kuliah
                if ( $r->jml_bayar_num > $r->tagihan ) {
                    return Response::json(['error' => 1, 'msg' => 'Jumlah yang dibayar lebih besar dari tagihan']);
                }
            }

            $id_mhs_reg = Sia::sessionMhs();
            $nim = Sia::sessionMhs('nim');
            $nama = Sia::sessionMhs('nama');
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
                'amount' => (int)$r->jml_bayar_num,
                'ket' => $r->ket,
                'exp_date' => "$exp_date"
            ];


            if ( !$this->createBriva($dataBriva) ) {
                return Response::json(['error' => 1, 'msg' => 'Server Gangguan, ulangi lagi beberapa saat kemudian.']);
            }

            DB::transaction(function()use($id_mhs_reg, $nim, $dataBriva, $r){
                $data = new Briva;
                $data->id_smt = Sia::sessionPeriode();
                $data->id_mhs_reg = $id_mhs_reg;
                $data->nim = $nim;
                $data->cust_code = $dataBriva['custCode'];
                $data->nama = $dataBriva['nama'];
                $data->jml = $r->jml_bayar_num;
                $data->ket = $r->ket;
                $data->jenis_bayar = $r->jenis_bayar;
                $data->exp_date = $dataBriva['exp_date'];
                $data->save();
            });
        }
        catch(\Exception $e)
        {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()]);
        }

        Session::flash('mail-reminder',$nim);

        return Response::json(['error' => 0, 'msg' => '']);
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

    public function delete($id)
    {
        $briva = Briva::where('id', $id)
            ->where('status', 'N')
            ->first();

        try {

            $mhs = Mahasiswareg::where('id', $briva->id_mhs_reg)->firstOrFail();

            if ( !empty($briva) ) {

                $status = $this->cekPembayaran($briva->cust_code);

                // Jika status pada web service 'N' maka do delete
                if ( $status && $status->data->statusBayar == 'N' ) {

                    $delete = $this->deleteBriva($briva->cust_code);

                    if ( $delete && $delete->status ) {
                        Briva::where('id', $id)->delete();
                    } else {
                        Rmt::error('Terjadi gangguan, mohon ulangi lagi');
                        return redirect()->back();
                    }

                    Session::flash('mail-batal',$mhs->nim);
                    Rmt::success('Pembayaran telah dibatalkan');
                } else {
                    Rmt::error('Terjadi gangguan. Atau Mungkin pembayaran ini telah selesai. Yang perlu anda lakukan: <br> - Apabila anda merasa telah membayar tagihan ini, Tunggu hingga pembayaran anda divalidasi oleh sistem.<br>
                        - Periksa koneksi internet anda, refresh halaman ini kemudian ulangi lagi.');
                }

            } else {

                Rmt::error('Pembayaran ini telah selesai');
            }

        } catch( \Exception $e ) {
            Rmt::error($e->getMessage());
            return redirect()->back();
        }

        return redirect()->back();
    }

    private function deleteExpired()
    {
        $today = Carbon::now();

        $briva = Briva::where('status', 'N')
                ->where('exp_date', '<', $today)
                ->get();

        foreach( $briva as $bri ) {
            $this->mailPembayaranBatal($bri->nim);
            Briva::where('id', $bri->id)->delete();
        }
    }

    private function mailPembayaranBatal($nim)
    {
        if ( empty($nim) ) {
            return Response::json(['error' => 1, 'msg' => 'Under Maintenance'], 200);
        }

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
        $mail['email']     = $mhs->email;

        $data['header'] = 'Hai '.$mhs->nm_mhs.', Pembayaran melalui BRI Virtual Account dibatalkan.';
        $data['msg'] = 'Pembayaran melalui BRI Virtual Account untuk pembayaran '.$jenis.' telah kami batalkan';
        $data['cust_code'] = $this->brivaNo.$bri->cust_code;
        $data['amount'] = 'Rp '.Rmt::rupiah($bri->jml);
        $data['exp_date'] = $bri->exp_date->format('d/m/Y H:i').' WITA';

        try {
            Mail::send('email.pembayaran-batal', $data, function ($message)use($mail)
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
    }

    public function historyTransaksi(Request $r)
    {
        $transaksi = Briva::where('nim', Sia::sessionMhs('nim'))
                ->where('status', 'Y')
                ->orderBy('id','desc')
                ->get();

        $loop = 1;
        foreach( $transaksi as $tr ) { ?>
            <tr>
                <td align="center"><?= $loop++ ?></td>
                <td align="center"><?= Carbon::parse($tr->created_at)->format('d/m/Y H:i') ?></td>
                <td>
                    <?php if ( $tr->jenis_bayar == 88 ) {
                        echo 'Tunggakan';
                    } elseif ( $tr->jenis_bayar == 99 ) {
                        echo 'Semester Pendek';
                    } elseif ( $tr->jenis_bayar == 0 ) {
                        echo 'Biaya Kuliah';
                    } else {
                        echo $tr->jenisBayar->ket;
                    } ?>
                </td>
                <td><?= $tr->ket ?></td>
                <td>Rp <?= Rmt::rupiah($tr->jml) ?></td>
            </tr>
        <?php }
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
                    ->where('id_prodi', $data['mhs']->id_prodi)
                    ->first();

        return $data;
    }

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
            // dd($create_data);
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

    private function destroyToken()
    {
        Session::pull('token');
        Session::pull('expired_token');
    }

    public function getToken()
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

            $error = '';
            $response = curl_exec($ch);
            if ( curl_error($ch) ) {
                $error = curl_error($ch);
            }
            curl_close($ch);
            
            $token = json_decode($response);
            dd($token);
            if ( !empty($token) && $token->status ) {

                Session::pull('token');
                Session::put('token', $token->data->access_token);

                Session::set('expired_token', Carbon::now()->addMinutes($token->data->expires_in/60));

                return true;
            } else {
                // throw new \Exception($token->responseDescription, 1);
                return false;
            }

        }
        catch( \Exception $e)
        {
            // return $e->getMessage();
            return false;
        }

    }

    public function curl($data, $token, $method, $header = ["Content-Type: application/json"], $param = '')
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
