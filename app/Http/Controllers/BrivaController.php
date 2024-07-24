<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Sia, Rmt, DB, Response, Session, Carbon;
use App\Briva;
use App\Pembayaran;
use App\Mahasiswareg;
use App\BrivaMember;

class BrivaController extends Controller
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

    public function akun(Request $r)
    {
        $akun = DB::table('briva_member as bm')
                        ->leftJoin('mahasiswa_reg as m1', 'bm.id_mhs_reg', 'm1.id')
                        ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                        ->leftJoin('prodi as pr', 'm1.id_prodi', 'pr.id_prodi')
                        ->select('bm.cust_code','m1.nim','m2.nm_mhs', 
                                    'm1.id as id_mhs_reg','pr.jenjang', 'pr.nm_prodi');

        if ( !empty($r->prodi) ) {
            $akun->where('m1.id_prodi', $r->prodi);
        }

        if ( !empty($r->angkatan) ) {
            $akun->whereRaw('left(nim,4)='.$r->angkatan);
        }

        if ( !empty($r->cari) ) {
            $akun->where(function($q)use($r){
                $q->where('m1.nim', 'like', '%'.$r->cari.'%')
                    ->orWhere('m2.nm_mhs', 'like', '%'.$r->cari.'%');
            });
        }

        $data['briva'] = $akun->orderBy('m1.nim')->paginate(20);

        return view('briva.akun', $data);
    }

    public function akunDelete($id)
    {
        try {

            $bri = BrivaMember::where('id_mhs_reg', $id)->firstOrFail();

            $data = array(
                'institutionCode' => $this->institutionCode,
                'brivaNo'=> $this->brivaNo,
                'custCode'=> $bri->cust_code
            );

            if ( $this->cekToken() ) {

                $header = ["Content-Type: application/json"];
                $token = Session::get('token');
                $delete = $this->curl($data, $token, 'delete', $header);
                $response = json_decode($delete);
// dd($response);
                if ( $response->status ) {

                    BrivaMember::where('id_mhs_reg', $id)->delete();
                    Rmt::success('Berhasil menghapus data');
                    
                } else {
                    Rmt::error($response->errDesc);
                }

                return redirect()->back();

            } else {
                return false;
            }
        } catch( \Exception $e )
        {
            Rmt::error($e->getMessage());
            return redirect()->back();
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

        if ( $method == 'post' ) {
            curl_setopt($ch, CURLOPT_POST, TRUE);
        } elseif ( $method == 'put' || $method == 'delete') {
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
}
