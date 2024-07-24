<?php

namespace App\Http\Controllers\mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB, Rmt, Session, Carbon, Response;
use App\Video, App\Resources;

class VideoController extends Controller
{

    public function add(Request $r, $id)
    {
        if ( !Session::has('m_id_dosen') ) {
            Session::set('m_id_dosen', $r->id_dosen);
        }

        $data['dosen'] = DB::table('dosen')->where('id', Session::get('m_id_dosen'))->first();

        $data['r'] = $this->jadwalKuliah($id);

        $data['telah_upload'] = Video::where('id_dosen', $r->id_dosen)
                        ->whereNotNull('file')
                        ->where('uploaded','y')
                        ->whereRaw("left(created_at, 10) = '".Carbon::now()->format('Y-m-d')."'")
                        ->count();
        
        return view('mobile.dsn-video.add', $data);
    }

    public function upload(Request $r)
    {
        try {
            if ( $r->hasFile('file') ) {

                $name = $r->file->getClientOriginalName();
                $destinationPath = config('app.video-files').'/'.Session::get('m_id_dosen');
                $r->file->move($destinationPath, $name);

                $data = new Video;
                $data->id_jadwal = $r->id_jadwal;
                $data->id_dosen = Session::get('m_id_dosen');
                $data->judul = Rmt::removeExtensi($name);
                $data->file = $name;
                $data->uploaded = 'n';
                $data->siap = 'n';
                $data->save();

                Session::set('id_video', $data->id);
                Session::set('file_video', $name);

                return Response::json(['id' => $data->id, 'file' => $name]);

            } else {
                return Response::json('Tidak ada file', 422);
            }
        } catch( \Exception $e ) {
            return Response::json($e->getMessage().'. Muat ulang halaman dan ulangi lagi', 422);
        }
    }

    public function store(Request $r)
    {
        $this->validate($r, [
            'judul' => 'required'
        ]);

        if ( $r->aksi == 'ambil' && empty($r->id_video) ) {
            return Response::json(['ID Video Youtube belum diisi'], 422);
        }

        try {

            DB::transaction(function()use($r,&$file, &$id_resource){

                if ( $r->aksi == 'ambil' ) {
                    $data = new Video;
                    $data->id_jadwal = $r->id_jadwal;
                    $data->id_dosen = Session::get('m_id_dosen');
                    $data->judul = $r->judul;
                    $data->video_id = trim($r->id_video);
                    $data->uploaded = 'y';
                    $data->siap = 'y';
                    $data->save();
                    
                    $file = '';
                    $id_resource = $data->id;

                    Rmt::success('Berhasil menyimpan data');

                } else {

                    $data = Video::find($r->id);
                    if ( empty($data) ) {
                        $data = Video::find(Session::get('id_video'));
                    }

                    $data->judul = $r->judul;
                    $data->ket = $r->ket;
                    $data->save();

                    $file = $data->file;
                    $id_resource = $data->id;

                }

                $urutan = Resources::where('id_jadwal', $r->id_jadwal)
                            ->where('pertemuan_ke', $r->pertemuan)
                            ->max('urutan');
                $urutan = empty($urutan) ? 1 : $urutan + 1; 

                $res = new Resources;
                $res->id_jadwal = $r->id_jadwal;
                $res->id_resource = $id_resource;
                $res->jenis = 'video';
                $res->pertemuan_ke = $r->pertemuan;
                $res->urutan = $urutan;
                $res->save();


            });

            Session::forget('id_video');
            Session::forget('file_video');

            return Response::json(['id' => $id_resource, 'aksi' => $r->aksi, 'judul' => $r->judul, 'file' => $file]);
            

        } catch(\Exception $e) {
            return Response::json([$e->getMessage().'. COBA ULANGI LAGI.'], 422);
        }
    }

    public function detail(Request $r, $id_jadwal, $id_video)
    {

        if ( !Session::has('m_id_dosen') ) {
            Session::set('m_id_dosen', $r->id_dosen);
        }

        $data['r'] = $this->jadwalKuliah($id_jadwal);

        $data['video'] = Video::findOrFail($id_video);

        return view('mobile.dsn-video.detail', $data);
    }

    public function updateKetersediaanVideo(Request $r)
    {
        $video = Video::find($r->id_video);
        $video->siap = 'y';
        $video->save();
    }

    public function edit(Request $r, $id, $id_video)
    {
        if ( !Session::has('m_id_dosen') ) {
            Session::set('m_id_dosen', $r->id_dosen);
        }

        $data['r'] = $this->jadwalKuliah($id);

        $data['video'] = Video::findOrFail($id_video);

        return view('mobile.dsn-video.edit', $data);
    }

    public function update(Request $r)
    {
        $this->validate($r, [
            'judul' => 'required',
            'id_video' => 'required'
        ]);

        try {

            DB::transaction(function()use($r){

                $data = Video::find($r->id);
                $data->judul = $r->judul;
                $data->ket = $r->ket;
                $data->video_id = trim($r->id_video);
                $data->save();

            });

            Rmt::success('Berhasil menyimpan data');

            return Response::json(['Ok']);
            

        } catch(\Exception $e) {
            return Response::json([$e->getMessage().'. COBA ULANGI LAGI.'], 422);
        }
    }

    public function deleteTmp(Request $r)
    {
        Session::forget('id_video');
        Session::forget('file_video');

        $data = Video::findOrFail(Session::get('id_video'));
        $path = config('app.video-files').'/'.Session::get('m_id_dosen');
        @unlink($path.'/'.$data->file);
        $data->delete();

        return redirect()->back();
    }

    private function jadwalKuliah($id_jdk)
    {
        $data = DB::table('jadwal_kuliah as jdk')
                ->leftJoin('nilai as n', 'jdk.id', 'n.id_jdk')
                ->leftJoin('mk_kurikulum as mkur','mkur.id','=','jdk.id_mkur')
                ->leftJoin('matakuliah as mk', 'jdk.id_mk','=','mk.id')
                ->select('jdk.id','mk.kode_mk', 'mk.nm_mk', 'jdk.kode_kls')
                ->where('jdk.id', $id_jdk)
                ->first();

        return $data;
    }
}