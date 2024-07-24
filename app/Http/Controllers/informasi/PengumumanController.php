<?php

namespace App\Http\Controllers\informasi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Session, Rmt, DB;
use App\InformasiModels\Pengumuman;

class PengumumanController extends Controller
{
    private $f = 'informasi.pengumuman.';
    private $prefix;
    
    public function __construct()
    {
        $this->prefix = env('DB_TABLE_PREFIX');
    }

    public function index(Request $r)
    {

        $pengumuman = Pengumuman::orderBy('created_at','desc');

        if ( $r->kat ) {
            $pengumuman->where('kategori', $r->kat);
        }

        $data['pengumuman'] = $pengumuman->paginate(20);

        return view($this->f."index", $data);
    }

    public function create()
    {
        return view($this->f."create");
    }

    public function store(Request $r)
    {
        $this->validate($r, [
                'judul' => 'required|max:150',
                'konten' => 'required'
            ]);

        $id = Uuid::uuid4();

        $deskripsi = str_limit(strip_tags($r->konten),90);
        $data = new Pengumuman;
        $data->id       = $id;
        $data->deskripsi= $deskripsi;
        $data->judul    = $r->judul;
        $data->kategori = $r->kategori;
        $data->konten   = $r->konten;
        $data->save();

        // Push notif
        $param = ['message' => $deskripsi, 'title' => $r->judul, 'id' => $id, 'kategori' => $r->kategori, 'jenis' => 'pengumuman'];
        $push_android = $this->pushNotif($param);

        $pa = json_decode($push_android);
        
        if ( $pa ) {
            $pesan = 'Sebanyak '.$pa->recipients.' perangkat yang menerima notifikasi';
        } else {
            $pesan = 'Tidak ada perangkat yang menerima notifikasi';
        }

        Rmt::Success('Berhasil menyimpan data');
        return redirect(route('pengumuman'));
    }


    private function pushNotif($data)
    {

        if ( $data['kategori'] == 'mahasiswa' ) {
            $app_id = "49e848e2-760f-49ed-a2cd-bfe038c4ef0f";
            $restApi = "OGUwOThhYzYtZTg4Ni00MzMzLTk3MGYtYTA3NmI3ODIyMjRk";
        } else {
            $app_id = "790f7cc2-33fa-41ba-80d0-29c8acbda3c3";
            $restApi = "ZWU0YjkxZjEtOTUzZC00Mzg4LWFlNGYtMzhkOGIxNzNhYzc4";
        }

        $content      = array(
            "en" => $data['message']
        );

        $heading = array(
           "en" => $data['title']
        );

        $fields = array(
            'app_id' => "$app_id",
            'included_segments' => array(
                'All'
            ),
            'data' => array(
                "id" => $data['id'],
                "jenis" => $data['jenis']
            ),
            'contents' => $content,
            'headings' => $heading
        );
        
        $fields = json_encode($fields);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic '.$restApi
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;

    }

    public function pushNotifIos($data)
    {
        $apiToken = env('IONIC_API_TOKEN');

        // The data to send to the API
        $postData = array(
            'send_to_all' => true,
            'profile' => 'nobelappprod',
            'notification' => [
                'message' => $data['message'],
                'title' => $data['title'],
                'payload' => [
                    'id' => $data['id'],
                    'jenis' => $data['jenis']
                ]
            ]
        );

        $headers = array(
                'Authorization: Bearer ' .$apiToken,
                'Content-Type: application/json'
            );

        // Setup cURL
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'https://api.ionic.io/push/notifications',
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => json_encode($postData)
        ));


        $response = curl_exec($ch);

    }

    public function edit($id)
    {
        $data['pengumuman'] = Pengumuman::find($id);

        return view($this->f.'edit', $data);
    }

    public function update(Request $r)
    {
        $this->validate($r, [
                'judul' => 'required|max:100',
                'konten' => 'required'
            ]);

        $data = Pengumuman::find($r->id);
        $data->judul    = $r->judul;
        $data->deskripsi= str_limit(strip_tags($r->konten),90);
        $data->kategori   = $r->kategori;
        $data->konten   = $r->konten;
        $data->save();

        Rmt::Success('Berhasil menyimpan data');
        return redirect()->back();
    }

    public function delete($id)
    {
        $val = explode(",", $id);

        foreach ($val as $v) {
            Pengumuman::where('id', $v)->delete();
        }

        Rmt::Success('Berhasil menghapus data');
        return redirect()->back();
    }

}
