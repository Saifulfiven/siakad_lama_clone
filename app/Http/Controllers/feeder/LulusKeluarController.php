<?php

namespace App\Http\Controllers\feeder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB, Sia, Rmt, Response, Session, Carbon, Excel, Feeder;
use App\Mahasiswareg;

class LulusKeluarController extends Controller
{
    public function index(Request $r)
    {
        try {
            if ( !Session::has('flk_periode') ) {
                $periode = [Sia::sessionPeriode()];
                Session::put('flk_periode', $periode);
            }

            return view('feeder.lulus-keluar.index');

        } catch( \Exception $e ) {
            $data['error'] = $e->getMessage();
            return view('feeder.lulus-keluar.index', $data); 
        }
    }

    public function data(Request $r)
    {
        try {

            $filter = $this->filter();
            
            // dd($filter);
            
            $param = [
                    'act' => 'GetListMahasiswaLulusDO',
                    'filter' => $filter,
                    'order' => "nim asc"
            ];

            $mahasiswa = Feeder::runWs($param);

            $data = [];

            $no = 1; 

            if ( is_array($mahasiswa->data) && count($mahasiswa->data) > 0 ) {

                foreach( $mahasiswa->data as $val ) {
                    $data[] = [
                        $no++,
                        $val->nim,
                        $val->nama_mahasiswa,
                        Feeder::nmProdi($val->id_prodi),
                        $val->nama_jenis_keluar,
                        Carbon::parse($val->tanggal_keluar)->format('d/m/Y'),
                        $val->id_periode_keluar
                    ];
                }
            } else {
                $data = [];
            }

            $result = [
                'aaData' => $data
            ];

            return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);

        }  catch( \Exception $e ) {
            return Response::json($e->getMessage(), 422);
        }
    }

    public function filter()
    {
        $filter = '';

        // Filter periode
            if ( Session::has('flk_periode') ) {
                if ( count(Session::get('flk_periode')) == 1 ) {

                    $filter .= "id_periode_keluar='".Session::get('flk_periode')[0]."' ";

                } else {

                    $no = 1;
                    foreach( Session::get('flk_periode') as $val ) {
                        if ( $no == 1 ) {
                            $filter .= "(id_periode_keluar='".$val."'";   
                            $filter .= " or "; 
                        } elseif ( $no > 1 && $no != count(Session::get('flk_periode')) ) {
                            $filter .= "id_periode_keluar='".$val."' ";
                            $filter .= " or ";
                        } elseif ( $no == count(Session::get('flk_periode')) ) {
                            $filter .= "id_periode_keluar='".$val."') ";
                        }
                        $no++;
                    }

                }

            } else {
                $periode = [Sia::sessionPeriode()];
                Session::put('flk_periode', $periode);
                $filter .= "id_periode_keluar='".Sia::sessionPeriode()."' ";
            }

        // Filter prodi
            if ( Session::has('flk_prodi') ) {
                $filter .= " and ";
                if ( count(Session::get('flk_prodi')) == 1 ) {

                    $filter .= "id_prodi='".Session::get('flk_prodi')[0]."' ";

                } else {

                    $no = 1;
                    foreach( Session::get('flk_prodi') as $val ) {
                        if ( $no == 1 ) {
                            $filter .= "(id_prodi='".$val."'";
                            $filter .= " or ";
                        } elseif ( $no > 1 && $no != count(Session::get('flk_prodi')) ) {
                            $filter .= "id_prodi='".$val."' ";
                            $filter .= " or ";
                        } elseif ( $no == count(Session::get('flk_prodi')) ) {
                            $filter .= "id_prodi='".$val."') ";
                        }
                        $no++;
                    }

                }
            }

        // Filter jenis keluar
            if ( Session::has('flk_jenis_keluar') ) {
                $filter .= " and ";
                if ( count(Session::get('flk_jenis_keluar')) == 1 ) {

                    $filter .= "id_jenis_keluar='".Session::get('flk_jenis_keluar')[0]."' ";

                } else {

                    $no = 1;
                    foreach( Session::get('flk_jenis_keluar') as $val ) {
                        if ( $no == 1 ) {
                            $filter .= "(id_jenis_keluar='".$val."'";
                            $filter .= " or ";
                        } elseif ( $no > 1 && $no != count(Session::get('flk_jenis_keluar')) ) {
                            $filter .= "id_jenis_keluar='".$val."' ";
                            $filter .= " or ";
                        } elseif ( $no == count(Session::get('flk_jenis_keluar')) ) {
                            $filter .= "id_jenis_keluar='".$val."') ";
                        }
                        $no++;
                    }

                }
            }

        // Pencarian
            if ( Session::has('flk_cari') && !empty(Session::get('flk_cari')) ) {
                $cari = Session::get('flk_cari');
                $filter .= " and (LOWER(nim) like '%".strtolower($cari)."%' or LOWER(nama_mahasiswa) like '%".strtolower($cari)."%')";
            }


        return $filter;
    }

    public function setFilter(Request $r)
    {

        Session::pull('flk_periode');
        Session::pull('flk_prodi');
        Session::pull('flk_jenis_keluar');

        if ( empty($r->periode) ) {
            $periode[] = Sia::sessionPeriode();
            Session::put('flk_periode', $periode);
        } else {
            $periode = [];
            foreach( $r->periode as $val ) {
                $periode[] = $val;
            }

            Session::put('flk_periode', $periode);
        }

        if ( count($r->prodi) > 0 && !empty($r->prodi[0]) ) {
            $prodi = [];
            foreach( $r->prodi as $val ) {
                $prodi[] = $val;
            }

            Session::put('flk_prodi', $prodi);
        }

        if ( !empty($r->jenis_keluar) > 0 && !empty($r->jenis_keluar[0]) ) {

            $jenis_keluar = [];
            foreach( $r->jenis_keluar as $val ) {
                $jenis_keluar[] = $val;
            }

            Session::put('flk_jenis_keluar', $jenis_keluar);
        }
        
        $this->data($r);

    }

    public function resetFilter(Request $r)
    {
        if ( $r->reset ) {

            if ( Session::has($r->reset) ) {

                if ( $r->reset == 'flk_periode' ) {

                    if ( count(Session::get($r->reset)) > 1 ) {

                        $this->resetSession($r);

                    } else {

                        return redirect()->back();
                    
                    }

                } else {

                    $this->resetSession($r);

                }

            }

        } else {

            Session::pull('flk_periode');
            Session::pull('flk_prodi');
            Session::pull('flk_jenis_keluar');
            Session::pull('flk_cari');

            $periode[] = Sia::sessionPeriode();
            Session::put('flk_periode', $periode);

        }

        return redirect()->back();
    }

    private function resetSession($r)
    {
        $filter_session = Session::get($r->reset);

        Session::pull($r->reset);

        // Ambil key jika ada dalam session
        if ( ( $key = array_search($r->value, $filter_session) ) !== false) {
            unset($filter_session[$key]);

            if ( !empty($filter_session) ) {
                $newData = [];
                foreach( $filter_session as $val ) {
                    $newData[] = $val;
                }
                Session::put($r->reset, $newData);
            } else {
                Session::pull($r->reset);
            }

        }

    }
    public function cari(Request $r)
    {
        if ( !empty($r->cari) ) {
            Session::put('flk_cari',$r->cari);
        } else {
            Session::pull('flk_cari');
        }

        return redirect(route('fdr_lk'));
    }

    public function ekspor(Request $r)
    {
        $filter = $this->filter();

        $param = [
                'act' => 'GetListMahasiswaLulusDO',
                'filter' => $filter,
                'order' => "nim asc"
        ];

        $data['mahasiswa'] = Feeder::runWs($param);

        try {
            Excel::create('Mahasiswa Lulus-Keluar Feeder', function($excel)use($data) {

                $excel->sheet('Sheet', function($sheet)use($data) {

                    $sheet->loadView('feeder.lulus-keluar.ekspor', $data);

                });

            })->download('xlsx');
        } catch(\Exception $e) {
            echo $e->getMessage();
        }
    }

}
