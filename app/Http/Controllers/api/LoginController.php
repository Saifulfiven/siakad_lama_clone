<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Rmt, Response, Hash;
use App\User;
use DB;

class LoginController extends Controller
{

    public function login(Request $r)
    {

        if ( empty($r->username ) ) {
            $fail = ['error' => 1, 'msg' => 'Username Masih Kosong'];
            return json_encode($fail);
        }

        if ( empty($r->password) ) {
            $fail = ['error' => 1, 'msg' => 'Password Masih Kosong'];
            return json_encode($fail);
        }

        $user = User::where('username', $r->username);

        if ( $user->count() > 0 ) {

            $user = $user->first();

            if ( Hash::check($r->password, $user->password) ) {

                if ( $user->level == 'mahasiswa' ) {

                    $mhs = DB::table('mahasiswa')
                        ->select('id','nm_mhs')
                        ->where('id_user', $user->id)->first();

                    $usr = DB::table('mahasiswa_reg')
                        ->where('id_mhs', $mhs->id)
                        ->orderBy('semester_mulai','desc')->first();

                    $data = [
                        'id' => $mhs->id,
                        'id_mhs_reg' => $usr->id, 
                        'nim' => $usr->nim, 
                        'nama' => $mhs->nm_mhs, 
                        'username' => $user->username,
                        'level' => $user->level
                    ];

                 } elseif ( $user->level == 'dosen' ) {

                    $usr = DB::table('dosen')
                        ->where('id_user', $user->id)->first();

                    $data = [
                        'id' => $usr->id,
                        'id_mhs_reg' => '', 
                        'nim' => '', 
                        'nama' => $usr->nm_dosen, 
                        'username' => $user->username,
                        'level' => $user->level
                    ];
                 } elseif ( $user->level == 'pengawas' ) {
                    $data = [
                        'id' => $user->id,
                        'id_mhs_reg' => '', 
                        'nim' => '', 
                        'nama' => $user->nama,
                        'username' => $user->username,
                        'level' => $user->level
                    ];
                 }

                $result = ['error' => 0, 'data' => $data];
            } else {
                $result = ['error' => 1, 'msg' => 'Username atau password salah' ];
            }

        } else {
            $result = ['error' => 1, 'msg' => 'Username atau password salah' ];
        }

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);

    }

}
