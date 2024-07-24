<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB, Rmt;

class SettingController extends Controller
{
    public function index(Request $r)
    {
    	$data['setting'] = DB::table('options')->get();

    	return view('setting.index', $data);
    }

    public function update(Request $r)
    {
    	$this->validate($r, [
    		'value' => 'required',
    	]);

        DB::table('options')->where('id', $r->id)->update(['value' => $r->value]);

    	Rmt::success('Berhasil menyimpan data');
    	return redirect()->back();
    }

}