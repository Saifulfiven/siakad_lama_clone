<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Mail, Rmt, Carbon, Feeder, Session, Sia, Auth, DB;
use App\User;
use App\Http\Controllers\api\Library;

class TestingController extends Controller
{
    use Library;
// DISABLE TO AVOID ADDED PERGURUAN TINGGI FRON ENDPOINT
//    public function inputPT(){
//       return DB::transaction(function (){
//            $uuidPT = Rmt::uuid();
//            DB::table('fdr_all_pt')->insert([
//                'id_perguruan_tinggi' => $uuidPT,
//                'kode_perguruan_tinggi' => '144001',
//                'nama_perguruan_tinggi' => 'Akademi Sekretari Dan Manajemen Indonesia Jayapura'
//            ]);
//            $prodis = [
//                [
//                    "kode_prodi" => "61405",
//                    "nama_prodi"=> "Manajemen Perusahaan",
//                    "jenjang" => "D3"
//                ],
//                [
//                    "kode_prodi" => "63412",
//                    "nama_prodi"=> "Sekretari",
//                    "jenjang" => "D3"
//                ]
//            ];
//            foreach ($prodis as $prodi){
//                $uuidProdi = Rmt::uuid();
//                $jenjang = $prodi['jenjang'];
//                $idJenjang = "0";
//                switch ($jenjang){
//                    case 'S1':
//                        $idJenjang = '30';
//                        break;
//                    case 'S2':
//                        $idJenjang = '35';
//                        break;
//                    case 'S3':
//                        $idJenjang = '40';
//                        break;
//                    case 'D3':
//                        $idJenjang = '22';
//                        break;
//                    default:
//                        break;
//                }
//
//                DB::table('fdr_all_prodi')->insert([
//                    'id_prodi' => $uuidProdi,
//                    'id_perguruan_tinggi' => $uuidPT,
//                    'kode_prodi' => $prodi['kode_prodi'],
//                    'nama_prodi' => $prodi['nama_prodi'],
//                    'id_jenjang' => $idJenjang,
//                    'nama_jenjang' => $prodi['jenjang'],
//                    'urut' => '',
//                ]);
//            }
//            return 'success';
//        });
//
//    }
    public function index()
    {
        try {
            $now = Carbon::parse('24-01-2021 07:57')->format('d-m-Y');
            dd($now);

        }catch(\Exception $e) {
            echo $e->getMessage();
        }

        // dd(Sia::getLastSp());
        $data = [
            'act' => 'GetListRiwayatPendidikanMahasiswa',
            'filter' => "nim='2015221922'",
            // 'filter' => "nama_mahasiswa='Sinai'",
            // 'filter' => "id_aktivitas = '8d83a607-3f74-4303-b63c-c0e30ab6ee58'",
            // 'filter' => "judul='ANALISIS INVESTASI TERHADAP CAPITAL BUDGETING PADA PERUSAHAAN RAJA INDO DI MAKASSAR'"
            'limit' => 10
        ];

        $data2 = [
            'act' => 'GetBiodataMahasiswa',
            'filter' => "id_mahasiswa='7c15ec97-902d-4f3e-aeb0-b409a8c4f16d'",
        ];

        $res = Feeder::runWs($data2);
        dd($res);
        // $id_reg_pd = $res->data[0]->id_registrasi_mahasiswa;
        // $id_reg_pd = '00b876a1-e162-4108-8768-fb7f420decd9';

        // $proxy = Feeder::initSoap();
        // $token = Feeder::getToken();

        // $data = $proxy->GetRecord($token, 'transkrip', 'p.id_reg_pd="'.$id_reg_pd.'"');
        // $data = $proxy->GetRecord($token, 'transkrip', 'p.nipd="2018212350"');

        // dd($data);

    }

    public function parseXml()
    {
        $xmlData = storage_path().'/file.xml';
        
        // dd($data);
        if ( !file_exists($xmlData) ) {
            dd('Pastikan anda telah menyimpan file xml di folder "storage" dengan nama "file.xml"');
        }

        $xml = simplexml_load_file($xmlData) or die("Error: Cannot create object"); ?>
        
        <table border="1">
            <tr>
                <th>No</th>
                <th>Judul</th>
                <th>Publisher</th>
                <th>Tahun</th>
            </tr>
        
        <?php $no = 1 ?>

        <?php foreach( $xml as $key => $val ) {
            $title = $val->titleInfo->title;
            $publisher = $val->originInfo->publisher;
            $terbitan = $val->originInfo->dateIssued; ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $title ?></td>
                    <td><?= $publisher ?></td> 
                    <td><?= $terbitan ?></td> 
                </tr>
        <?php } ?>
        </table>

        <?php
        // dd($xml);
    }

    public function zoom()
    {
        $token = $this->getToken();
        // dd($token);
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.zoom.us/v2/users/abd.rahmat.ika@gmail.com/meetings?page_number=1&page_size=30&type=live",
          CURLOPT_RETURNTRANSFER => true,
          // CURLOPT_SSL_VERIFYHOST => false,
          // CURLOPT_SSL_VERIFYPEER => true,
          // CURLOPT_CAINFO => getcwd() . "/positiveSSL.ca-bundle"),
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "authorization: Bearer ".$token
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          echo $response;
        }
    }

    private function getToken()
    {
        $exp = Carbon::now()->addDays(2)->timestamp;
        $apiKey = '1wVYrvLGSKip7H2AaLbnNQ';
        $secretKey = 'ISiZgIDNzG7RcPUPqCvogdapAZ1IUssqZjyR';

        // Buat Array untuk header lalu convert menjadi JSON
        $header = json_encode(['alg'=>'HS256','typ'=>'JWT']);
        // Encode header menjadi Base64Url String
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

        // Buat Array payload lalu convert menjadi JSON
        $payload = json_encode(['iss'=> $apiKey, 'exp' => $exp]);
        // Encode Payload menjadi Base64Url String
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));  

        // Buat Signature dengan metode HMAC256
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secretKey, true);
        // Encode Signature menjadi Base64Url String
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        // Gabungkan header, payload dan signature dengan tanda titik (.)
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        // Tampilkan JWT
        return $jwt;
    }

    private function tesMail()
    {
        try {
            $data[] = '';
            Mail::send('email.tes', $data, function ($message)
            {
                $message->from('pmb@stienobel-indonesia.ac.id','Tes STIE Nobel Indonesia');
                $message->to('abd.rahmat.ika@gmail.com');
                $message->subject('tes');
            });
        } catch(\Exception $e)
        {
            echo $e->getMessage();
        }
    }
}
