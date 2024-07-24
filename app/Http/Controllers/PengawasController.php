<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pengawas;
use DB, Response, Auth, Rmt;

class PengawasController extends Controller
{
    public function index(Request $r)
    {
    	$data['pengawas'] = Pengawas::orderBy('id','desc')->paginate(20);

    	return view('pengawas.index', $data);
    }

    public function store(Request $r)
    {
    	$this->validate($r, [
    		'nama' => 'required|unique:pengawas',
    	]);

        $count = DB::table('pengawas')->where('nama', $r->nama)->count();
        if ( $count > 0 ) {
            Rmt::error('Pengawas ini telah ada');
        }

    	DB::table('pengawas')->insert(['nama' => $r->nama]);

    	Rmt::success('Berhasil menyimpan data');
    	return redirect()->back();
    }

    public function update(Request $r)
    {
    	$this->validate($r, [
    		'nama' => 'required|unique:pengawas,id,'.$r->id,
    	]);

        DB::table('pengawas')->where('id', $r->id)->update(['nama' => $r->nama]);

    	Rmt::success('Berhasil menyimpan data');
    	return redirect()->back();
    }

    public function delete($id)
    {
    	$count = DB::table('jadwal_ujian')->where('id_pengawas',$id)->count();
    	if ( $count > 0 ) {
    		Rmt::error('Gagal menghapus, pengawas sedang terpakai pada modul jadwal ujian');
    		return redirect()->back();
    	}

    	Pengawas::find($id)->delete();
    	Rmt::success('Berhasil menghapus data');
    	return redirect()->back();
    }

}