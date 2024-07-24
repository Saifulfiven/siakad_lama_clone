<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use DB, Response, Auth, Rmt, Session, Sia;

class UsersController extends Controller
{
    public function index(Request $r)
    {

    	$users = User::orderBy('created_at','desc');

        if ( !empty( $r->level) ) {
            Session::set('user_level', $r->level);
        }

        if ( !Session::has('user_level') ) {
            Session::set('user_level', 'all');
        }

        if ( Session::get('user_level') != 'all' ) {
            $users->where('level', Session::get('user_level'));
        }

    	if ( !empty($r->search) ) {
    		$users->where(function($q)use($r) {
    			$q->where('nama','like', '%'.$r->search.'%')
                    ->orWhere('email','like', '%'.$r->search.'%')
                    ->orWhere('username','like', '%'.$r->search.'%');
    		});
    	}

    	$data['users'] = $users->paginate(20);

    	return view('users.index', $data);
    }

    public function add(Request $r)
    {
        if ( !empty($r->fakultas) ) {

            $fakultas = $r->fakultas == 'all' ? Sia::listProdi() : Sia::listProdi($r->fakultas);

            foreach( $fakultas as $pr ) { ?>
                <li>
                    <input type="checkbox" id="<?= $pr->id_prodi ?>" name="jurusan[]" value="<?= $pr->id_prodi ?>" <?= is_array(old('jurusan')) && count(old('jurusan')) > 0 && in_array($pr->id_prodi, old('jurusan')) ? 'checked':'' ?>>
                    <label for="<?= $pr->id_prodi ?>"><?= $pr->jenjang .' '.$pr->nm_prodi ?></label>
                </li>
        <?php }
            exit;
        }
    	return view('users.add');
    }

    public function store(Request $r)
    {
    	$this->validate($r, [
    		'nama' => 'required',
            'username' => 'required|unique:users',
            'password' => 'required|min:6',
            'email' => 'required|email|unique:users',
    	]);

        try {
            DB::transaction(function()use($r){

                $id = Rmt::uuid();
                $data = new User;
                $data->id = $id;
                $data->nama = $r->nama;
                $data->username = $r->username;
                $data->email = $r->email;
                $data->level = $r->level;
                $data->naik_smt = $r->naik_smt;
                $data->password = bcrypt($r->password);
                $data->save();

                if ( is_array($r->jurusan) && count($r->jurusan) > 0 ) {
                    foreach( $r->jurusan as $val ) {
                        $data = ['id_user' => $id, 'id_prodi' => $val];
                        DB::table('user_roles')->insert($data);
                    }
                }

            });
        } catch( \Exception $e) {
            Rmt::error($e->getMessage());
            return redirect()->back()->withInput();
        }

    	Rmt::success('Berhasil menyimpan data');
    	return redirect(route('users'));
    }

    public function edit(Request $r)
    {
    	$data['user'] = User::find($r->id);
    	if ( !$data['user'] ) {
    		echo "<center>NOT FOUND</center>";
    		exit;
    	}

        $role = [];

        foreach( $data['user']->roles as $rol ) {
            $role[] = $rol->prodi->id_prodi;
        }

        $data['role'] = $role;
    
    	return view('users.edit', $data);
    }

    public function update(Request $r)
    {
        $this->validate($r, [
            'nama' => 'required',
            'username' => 'required|unique:users,id,'.$r->id,
            'password' => 'min:6',
            'email' => 'email|unique:users,id,'.$r->id,
        ]);

        try {
            DB::transaction(function()use($r){
                $data = User::find($r->id);
                $data->nama = $r->nama;
                $data->username = $r->username;
                $data->email = $r->email;
                $data->level = $r->level;
                $data->naik_smt = $r->naik_smt;
                if ( !empty($r->password) ) {
                    $data->password = bcrypt($r->password);
                }
                $data->save();

                DB::table('user_roles')->where('id_user', $r->id)->delete();
                
                if ( count($r->jurusan) > 0 ) {
                    foreach( $r->jurusan as $val ) {
                        $data = ['id_user' => $r->id, 'id_prodi' => $val];
                        DB::table('user_roles')->insert($data);
                    }
                }
            });
        } catch( \Exception $e) {
            Rmt::error($e->getMessage());
            return redirect()->back()->withInput();
        }

        Rmt::success('Berhasil menyimpan data');
        return redirect()->back();
    }

    public function profil(Request $r)
    {
        $data['user'] = User::find(Auth::user()->id);

        return view('users.ubah-profil', $data);
    }

    public function updateProfil(Request $r)
    {
        $this->validate($r, [
            'nama' => 'required',
            'username' => 'required|unique:users,id,'.$r->id,
            'password' => 'min:6',
            'email' => 'email|unique:users,id,'.$r->id,
        ]);

        try {
            DB::transaction(function()use($r){
                $data = User::find($r->id);
                $data->nama = $r->nama;
                $data->username = $r->username;
                $data->email = $r->email;
                if ( !empty($r->password) ) {
                    $data->password = bcrypt($r->password);
                }
                $data->save();

            });
        } catch( \Exception $e) {
            Rmt::error($e->getMessage());
            return redirect()->back()->withInput();
        }

        Rmt::success('Berhasil menyimpan data');
        return redirect()->back();
    }

    public function delete($id)
    {
        DB::transaction(function()use($id){
        	User::find($id)->delete();
            DB::table('user_roles')->where('id_user', $id)->delete();
        });

    	Rmt::success('Berhasil menghapus data');
    	return redirect()->back();
    }

    public function reLogin($id_user)
    {
        Session::put('current_admin', Auth::user()->id);
        Session::put('switch_from', 'user');
        $user = User::find($id_user);
        Auth::login($user);
        Session::pull('periode_aktif');
        return redirect(url('/beranda'));
    }

}