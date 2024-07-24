<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use App\Dosen, App\User, App\KegiatanDosen;
use DB, Sia, Rmt, Excel, Response, Session, Auth, Zipper;

class DosenController extends Controller
{
	use DosenMengajar;
	
    public function index(Request $r)
    {

    	if ( !Session::has('dosen') ) {
	    	Session::put('dosen.cari','');
	    }
    	$query = Sia::dosen();
    	$data['dosen'] = $query->paginate(10);
    	return view('dosen.index', $data);
    }

	public function cari(Request $r)
	{
		if ( !empty($r->cari) ) {
			Session::put('dosen.cari',$r->cari);
		} else {
			Session::pull('dosen.cari');
		}

		return redirect(route('dosen'));
    }

    public function filter(Request $r)
    {
		if ( !empty($r->modul) ) {

			if ( $r->modul == 'jenis' ) {

				if ( $r->val == 'DTY,DPK' ) {
					Session::put('dosen.jenis2',$r->val);
					Session::pull('dosen.jenis');
				} elseif ( $r->val == 'all' ) {
					Session::pull('dosen.jenis');
					Session::pull('dosen.jenis2');
				} else {
					Session::put('dosen.jenis',$r->val);
					Session::pull('dosen.jenis2');
				}

			} else {
			
				if ( $r->val == 'all' ) {
					Session::pull('dosen.'.$r->modul);
				} else {
					Session::put('dosen.'.$r->modul,$r->val);
				}

			}
		}

		if ( $r->remove ) {
			Session::pull('dosen');
		}

		return redirect(route('dosen'));
    }

    public function eksporExcel(Request $r)
    {
    	$query = Sia::dosen();
    	$data['dosen'] = $query->get();

			try {
				Excel::create('Dosen', function($excel)use($data) {

				    $excel->sheet('New sheet', function($sheet)use($data) {

				        $sheet->loadView('dosen.excel', $data);

				    });

				})->download('xlsx');
			} catch(\Exception $e) {
				echo $e->getMessage();
			}
    }

    public function eksporPrint(Request $r)
    {
    	$query = Sia::dosen();

    	$data['dosen'] = $query->get();
    	return view('dosen.print', $data);
    }

    public function add(Request $r)
    {
    	return view('dosen.add');
    }

    public function store(Request $r)
    {
    	$this->validate($r, [
    		'nama' => 'required',
    		'nidn' => 'unique:dosen',
    		'jenis_dosen' => 'required',
    	]);

    	$data = [
    		'id_dosen' => Rmt::uuid(),
    		'id_user' => Rmt::uuid(),
    		'username' => empty($r->nidn) ? rand(100000,999999) : $r->nidn,
    		'password' => '12345678'
    	];

    	try{
	    	DB::transaction(function()use($r,$data){
	    		$user = new User;
				$user->id = $data['id_user'];
				$user->nama = trim($r->nama);
				$user->username = $data['username'];
				$user->email = empty($r->email) ? $data['username'].'@stienobel-indonesia.ac.id' : $r->email;
				$user->password = bcrypt($data['password']);
				$user->level = 'dosen';
				$user->save();

	    		$dsn = new Dosen;
	    		$dsn->id = $data['id_dosen'];
	    		$dsn->id_prodi = $r->prodi;
	    		$dsn->id_user = $data['id_user'];
	    		$dsn->nm_dosen = trim($r->nama);
	    		$dsn->gelar_depan = trim($r->gelar_depan);
	    		$dsn->gelar_belakang = trim($r->gelar_belakang);
	    		$dsn->pendidikan_tertinggi = $r->pendidikan_tertinggi;
	    		$dsn->aktivitas = $r->aktivitas;
	    		$dsn->jabatan_fungsional = $r->jabatan_fungsional;
	    		$dsn->golongan = $r->golongan;
	    		$dsn->nip = $r->nip;
	    		$dsn->nidn = $r->nidn;
	    		$dsn->tempat_lahir = $r->tempat_lahir;
	    		$dsn->tgl_lahir = empty($r->tgl_lahir) ? NULL : Rmt::formatTgl($r->tgl_lahir,'Y-m-d');
	    		$dsn->jenkel = $r->jenis_kelamin;
	    		$dsn->jenis_dosen = $r->jenis_dosen;
	    		$dsn->id_agama = empty($r->agama) ? NULL : $r->agama;
	    		$dsn->alamat = $r->alamat;
	    		$dsn->hp = $r->hp;
	    		$dsn->aktif = 1;
	    		$dsn->save();
	    	});
	    } catch( \Exception $e) {
	    	return Response::json(['error' => 1,'msg' => $e->getMessage()]);
	    }

	    Rmt::success('Berhasil menyimpan data');
	    return Response::json(['error' => 0, 'msg' => 'sukses']);
    }

    public function impor(Request $r)
    {
        if ( $r->hasFile('file') ) {

            if ( !is_dir(storage_path().'/tmp') ) {
                mkdir(storage_path().'/tmp');
            }

            $nama_file = $r->file('file')->getClientOriginalName();
            $r->file('file')->move(storage_path().'/tmp', $nama_file);

	        Excel::load(storage_path().'/tmp/'.$nama_file, function($reader)use($nama_file,&$dosen,&$users) {
	            $results = $reader->get();

	            foreach( $results as  $r ) {

	            	$id_user = Rmt::uuid();
	            	$username = empty(trim($r->nidn)) ? rand(100000,999999) : $r->nidn;
    				$password = '12345678';

	            	$users[] = [
						'id' => $id_user,
						'nama' => trim($r->nama),
						'username' => $username,
						'email' => empty($r->email) ? $username.'@stienobel-indonesia.ac.id' : $r->email,
						'password' => bcrypt($password),
						'level' => 'dosen'
					];

                	$dosen[] = [
	                	'id' => Rmt::uuid(),
			    		'id_user' => $id_user,
			    		'id_prodi' => $r->id_prodi,
			    		'nidn' => $r->nidn,
			    		'nm_dosen' => trim($r->nm_dosen),
			    		'gelar_depan' => trim($r->gelar_depan),
			    		'gelar_belakang' => trim($r->gelar_belakang),
			    		'pendidikan_tertinggi' => trim($r->pendidikan_tertinggi),
			    		'nip' => $r->nip,
			    		'tempat_lahir' => $r->tempat_lahir,
			    		'tgl_lahir' => empty($r->tgl_lahir) ? NULL : Rmt::formatTgl($r->tgl_lahir,'Y-m-d'),
			    		'id_agama' => empty($r->id_agama) ? NULL : $r->id_agama,
			    		'jenkel' => $r->jenkel,
			    		'jenis_dosen' => $r->jenis_dosen,
			    		'jabatan_fungsional' => $r->jabatan_fungsional,
			    		'golongan' => $r->golongan,
			    		'aktivitas' => $r->aktivitas,
			    		'alamat' => $r->alamat,
			    		'hp' => $r->hp,
			    		'aktif' => 1
		           ];
	            }

	        });
	        
	        try {
		        DB::transaction(function()use($dosen,$users) {
		        	DB::table('users')->insert($users);
		        	DB::table('dosen')->insert($dosen);
		        });
		        $response = ['error' => 0, 'msg' => 'Sukses memasukkan data'];
		    } catch(\Exception $e) {
				$response = ['error' => 1, 'msg' => $e->getMessage()];
			}

        } else {
            $response = ['error' => 1, 'msg' => 'Tidak file dipilih'];
        }

        if ( file_exists(storage_path().'/tmp/'.$nama_file) ) {
        	unlink(storage_path().'/tmp/'.$nama_file);
        }

        return Response::json($response,200);
    }

    public function edit($id)
    {
    	$data['dsn'] = DB::table('dosen as d')
    					->leftJoin('users as u', 'd.id_user', '=', 'u.id')
    					->select('d.*','u.username','u.email')
    					->where('d.id', $id)->first();

    	return view('dosen.edit', $data);
    }

    public function update(Request $r)
    {
    	$this->validate($r, [
    		'nama' => 'required',
    		'nidn' => 'unique:dosen,id,'.$r->id,
    		'jenis_dosen' => 'required',
    		'username' => 'required'
    	]);

    	try{
	    	DB::transaction(function()use($r){
	    		$user = User::find($r->id_user);
	    		if ( empty($user) ) {
	    			$id_user = Rmt::uuid();
	    			$mhs = new User;
                	$mhs->id = $id_user;
	                $mhs->username = $r->username;
	                $mhs->nama = $r->nama;
	                $mhs->email = $r->username.'@stienobel-indonesia.ac.id';
	                $mhs->level = 'dosen';
	                $mhs->password = bcrypt($r->password);
	                $mhs->save();
	    		} else {
					$user->nama = trim($r->nama);
					$user->username = trim($r->username);
					$user->email = $r->email;
					if ( !empty($r->password) ) {
						$user->password = bcrypt($r->password);
					}
					$user->save();
				}

	    		$dsn = Dosen::find($r->id);
	    		$dsn->id_prodi = $r->prodi;
	    		if ( isset($id_user) ) {
	    			$dsn->id_user = $id_user;
	    		}
	    		$dsn->nm_dosen = trim($r->nama);
	    		$dsn->gelar_depan = trim($r->gelar_depan);
	    		$dsn->gelar_belakang = trim($r->gelar_belakang);
	    		$dsn->pendidikan_tertinggi = $r->pendidikan_tertinggi;
	    		$dsn->aktivitas = $r->aktivitas;
	    		$dsn->jabatan_fungsional = $r->jabatan_fungsional;
	    		$dsn->golongan = $r->golongan;
	    		$dsn->nip = $r->nip;
	    		$dsn->nidn = $r->nidn;
	    		$dsn->tempat_lahir = $r->tempat_lahir;
	    		$dsn->tgl_lahir = empty($r->tgl_lahir) ? NULL : Rmt::formatTgl($r->tgl_lahir,'Y-m-d');
	    		$dsn->jenkel = $r->jenis_kelamin;
	    		$dsn->jenis_dosen = $r->jenis_dosen;
	    		$dsn->id_agama = empty($r->agama) ? NULL : $r->agama;
	    		$dsn->alamat = $r->alamat;
	    		$dsn->hp = $r->hp;
	    		$dsn->aktif = $r->aktif;
	    		$dsn->save();
	    	});
	    } catch( \Exception $e) {
	    	return Response::json(['error' => 1,'msg' => $e->getMessage()]);
	    }

	    Rmt::success('Berhasil menyimpan data');
	    return Response::json(['error' => 0, 'msg' => 'sukses']);
    }

    public function delete($id)
    {
    	$rule = DB::table('dosen_mengajar')->where('id_dosen', $id)->count();

    	if ( $rule > 0 ) {
    		Rmt::error('Gagal menghapus, dosen ini terpakai');
    		return redirect()->back();
    	}

    	Dosen::find($id)->delete();
	    Rmt::success('Berhasil menghapus data');
	    return redirect()->back();
    }

    public function login($id_user) {
        Session::put('current_admin', Auth::user()->id);
        Session::put('switch_from', 'dosen');
    	$user = User::find($id_user);
        Auth::login($user);
        Session::pull('periode_aktif');
        return redirect(url('/beranda'));
    }

    public function kegiatan()
    {
    	return view('dosen.kegiatan.index');
    }

    public function kegiatanData(Request $r)
    {
		## Read value
		$draw = $r->draw;
		$start = $r->start ? $r->start : 0;
		$rowperpage = $r->length ? $r->length : 10;

		$search_arr = $r->search;
		/*
		$columnIndex_arr = $r->order;
		$columnName_arr = $r->columns;
		$order_arr = $r->order;

		$columnIndex = $columnIndex_arr[0]['column']; // Column index
		$columnName = $columnName_arr[$columnIndex]['data']; // Column name
		$columnSortOrder = $order_arr[0]['dir']; // asc or desc
		*/
		$searchValue = $search_arr['value']; // Search value

		// Total records
		$totalRecords = KegiatanDosen::groupBy('id_dosen')->get()->toArray();

		$totalRecordswithFilter = DB::table('dosen_kegiatan as dk')
									->join('dosen as d', 'dk.id_dosen', 'd.id')
									->select('dk.id_dosen')
									->where('d.nm_dosen', 'like', '%'.$searchValue.'%')
									->groupBy('dk.id_dosen')
									->get()
									->toArray();

		// Fetch records
	    $records = DB::table('dosen_kegiatan as dk')
						->join('dosen as d', 'dk.id_dosen', 'd.id')
						->select('dk.id', 'dk.id_dosen','d.nm_dosen', 'd.gelar_depan','d.gelar_belakang',
							DB::raw('(SELECT count(*) from dosen_kegiatan
										where id_dosen=dk.id_dosen) as jml_kegiatan'))
						->where('d.nm_dosen', 'like', '%'.$searchValue.'%')
						->skip($start)
						->take($rowperpage)
						->groupBy('dk.id_dosen')
						// ->orderBy($columnName, $columnIndex)
						->get();

		$data_arr = [];
		
		$no = $start + 1;

		foreach( $records as $rec )
		{
			$link_open = '<a href="'.route('dosen_kegiatan_detail', ['id_dosen' => $rec->id_dosen]).'" class="btn btn-default btn-sm"><i class="fa fa-search-plus"></i> Buka</a>';
			$link_download = '<a href="'.route('dosen_kegiatan_download', ['id_dosen' => $rec->id_dosen]).'" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-download"></i> Download Dokumen</a>';

			$data_arr[] = [
				'no' => $no++,
				'nm_dosen' => Sia::namaDosen($rec->gelar_depan, $rec->nm_dosen, $rec->gelar_belakang),
				'jml_kegiatan' => $rec->jml_kegiatan,
				'aksi' => $link_open.' &nbsp; '.$link_download
			];
		}

		$response = [
			'draw' => intval($draw),
			'iTotalRecords' => count($totalRecords),
			'iTotalDisplayRecords' => count($totalRecordswithFilter),
			'aaData' => $data_arr
		];

		return Response::json($response);


    }

    public function kegiatanDetail($id_dosen)
    {
    	$data['dosen'] = Dosen::findOrFail($id_dosen); 
    	$kegiatan = KegiatanDosen::where('id_dosen', $id_dosen)
    						->orderBy('created_at', 'desc')
    						->orderBy('tahun', 'desc');

    	if ( Session::has('kegiatan.tahun') ) {
    		$kegiatan->where('tahun', Session::get('kegiatan.tahun'));
    	}

    	if ( Session::has('kegiatan.kategori') ) {
    		$kegiatan->where('id_kategori', Session::get('kegiatan.kategori'));
    	}

    	$data['kegiatan'] = $kegiatan->get();

    	$data['tahun'] = KegiatanDosen::where('id_dosen', $id_dosen)
    					->select('tahun')
    					->groupBy('tahun')
    					->orderBy('tahun','desc')
    					->get();

    	return view('dosen.kegiatan.detail', $data);
    }

    public function kegiatanFilter(Request $r)
    {
		if ( !empty($r->modul) ) {

			if ( $r->val == 'all' ) {
				Session::pull('kegiatan.'.$r->modul);
			} else {
				Session::put('kegiatan.'.$r->modul,$r->val);
			}
		}

		if ( $r->remove ) {
			Session::pull('kegiatan');
		}

		return redirect()->back();
    }

    public function kegiatanViewDok($id, $id_dosen, $file)
    {
    	$kegiatan= KegiatanDosen::where('id', $id)
    						->where('id_dosen', $id_dosen)
    						->firstOrFail();

    	$path = config('app.kegiatan-dosen');
    	$file = $path.'/'.$kegiatan->id_dosen.'/'.$kegiatan->file;
    	
    	if ( file_exists($file) ) {
            return Response::file($file);
        } else {
            echo "<center><h4>File tidak ditemukan</h4></center>";
        }

    }

    public function kegiatanDownload(Request $r, $id_dosen)
    {
    	$dosen = Dosen::findOrFail($id_dosen);

        try {

        	$txt = config('app.kegiatan-dosen').'/petunjuk.txt';
            $files = config('app.kegiatan-dosen').'/'.$r->id_dosen;
            $fileTmp = storage_path('tmp').'/'.$dosen->nm_dosen.'.zip';
            Zipper::make($fileTmp)->add($files)->add($txt)->close();
            
            return Response::download($fileTmp)->deleteFileAfterSend(true);

        } catch(\Exception $e){
            Rmt::error('Gagal mendownload data: '.$e->getMessage());
            return redirect()->back();
        }
    }

    public function kegiatanDelete($id)
    {
    	$kegiatan = KegiatanDosen::findOrFail($id);

    	$path = config('app.kegiatan-dosen');
    	$file = $path.'/'.$kegiatan->id_dosen.'/'.$kegiatan->file;

    	if ( file_exists($file) ) {
    		unlink($file);
    	}

    	$kegiatan->delete();

    	Rmt::success('Berhasil menghapus data');

    	return redirect()->back();

    }
}
