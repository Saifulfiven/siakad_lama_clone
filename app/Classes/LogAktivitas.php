<?php

namespace App\Classes;
use Request, Auth;
use App\UserActivity;

class LogAktivitas
{

	public static function add($aktivitas, $level = null)
	{
    	try {
    		$level_ = Auth::user()->level;

    		if ( $level_ == 'mahasiswa' || $level_ == 'dosen' ) {
		    	$data = new UserActivity;
		    	$data->user_id = Auth::check() ? Auth::user()->id : 0;
		    	$data->nama = Auth::check() ? Auth::user()->nama : '-';
		    	$data->level = Auth::check() ? $level_ : $level;
		    	$data->aktivitas = Request::method().' - '.$aktivitas;
		    	$data->url = Request::fullUrl();
		    	// $data->ip = Request::ip();
		    	$data->save();
		    }
	    
	    } catch( \Exception $e ) {
	    	// dd($e->getMessage());
	    }
	}

	public static function addManual($user_id, $nama, $aktivitas, $level = 'mahasiswa')
	{
    	try {

	    	$data = new UserActivity;
	    	$data->user_id = $user_id;
	    	$data->nama = $nama;
	    	$data->level = $level;
	    	$data->aktivitas = Request::method().' - '.$aktivitas;
	    	$data->url = Request::fullUrl();
	    	$data->ip = Request::ip();
	    	$data->save();
	    
	    } catch( \Exception $e ) {
	    	// dd($e->getMessage());
	    }
	}
}