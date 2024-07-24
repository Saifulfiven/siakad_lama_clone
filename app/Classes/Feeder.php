<?php

namespace App\Classes;
use DB, Carbon, Config;

class Feeder
{
	public $url;
	private $token;

	public function __construct()
	{
		$this->url = Config::get('app.feeder_url');
		$this->token = $this->getToken();
		// if ( $this->token ) {
		// 	Session::put('feeder_token', $this->token);
		// }
	}

	public function runWs($data = '')
	{
		try {

		    $ch = curl_init();

		    if ( @$data['act'] != 'GetToken' ) {
		    	$token = ['token' => $this->token];
		    	$data = array_merge($data, $token);
		    }

		    curl_setopt($ch, CURLOPT_URL, $this->url);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($ch, CURLOPT_HEADER, FALSE);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		    curl_setopt($ch, CURLOPT_POST, TRUE);

		    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

		    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		      "Content-Type: application/json"
		    ));

		    $response = curl_exec($ch);
		    $err = curl_error($ch);
		    curl_close($ch);

		    if ($err) {
		    	// dd($err);
		       	// $response = ['error_code' => 1, 'error_desc' => $err];
		       	abort(422, $err);
	        	// return $response;
	        } else {
		    	return json_decode($response);
	        }
	    } catch( \Exception $e ) {
	    	abort(422, $e->getMessage());
	    }
	}

	public function getToken()
	{
		try {

			$data = ['act' => 'GetToken', 'username' => Config::get('app.feeder_username'), 'password' => Config::get('app.feeder_password')];

			$token = $this->runWs($data);
			// dd($token);

			if ( is_object($token) && $token->error_code == 0 ) {
				return $token->data->token;
			} else {
				// return false;
				abort(422, 'Gagal mengambil token, periksa jaringan dan muat ulang halaman');
			}

		 } catch( \Exception $e ) {
	    	abort(422, $e->getMessage());
	    }
	}

	public function idProdi($id)
	{
		$data = [
		    '61201' => '83abea40-7961-4a47-a8e0-b1bb1f348495',
		    '62201' => '47fb92cf-c04b-465a-9e64-70e727577804',
		    '61101' => '14907a02-4a9e-4a0b-934b-09d09572d936',
		    '59201' => '06dcaec4-9dd9-44aa-847e-4ac7ec9ec302'
		];

		return $data[$id];
	}

	public function nmProdi($id = null)
	{
		$data = [
		    '83abea40-7961-4a47-a8e0-b1bb1f348495' => 'S1 Manajemen',
		    '47fb92cf-c04b-465a-9e64-70e727577804' => 'S1 Akuntansi',
		    '14907a02-4a9e-4a0b-934b-09d09572d936' => 'S2 Manajemen',
		    '06dcaec4-9dd9-44aa-847e-4ac7ec9ec302' => 'S1 STI'
		];

		return empty($id) ? $data : $data[$id];
	}

	/* V1. Menggunakan nusoap */
	public function initSoap()
	{
		require_once('nusoap/new/nusoap.php');

        $url = config('app.feeder_url2');
        $client = new \nusoap_client($url, true);
        
        $proxy = $client->getProxy();
        
        return $proxy;
	}

	public function getTokenNusoap()
	{
		$proxy = $this->initSoap();

        $username = config('app.feeder_username');
        $password = config('app.feeder_password');
        $token = $proxy->GetToken($username, $password);

        return $token;
	}
}