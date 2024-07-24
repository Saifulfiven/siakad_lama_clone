<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB, Sia, Rmt, Session, Excel;
use App\Briva, App\BrivaMember;
class TunggakanController extends Controller
{
    public function index(Request $r)
    {

        // Set Filter
            if ( !empty($r->angkatan) ) {
                if ( $r->angkatan == 'all' ) {
                    Session::pull('tu_smtin');
                } 
                Session::set('tu_angkatan', $r->angkatan);
            }

            if ( !empty($r->smtin) ) {
                Session::set('tu_smtin', $r->smtin);
            }

            if ( !empty($r->prodi) ) {
                Session::set('tu_prodi', $r->prodi);
            }

            if ( !empty($r->status) || $r->status === '0' ) {
                Session::set('tu_status', $r->status);
            }

            if ( !Session::has('tu_status') ) {
                $this->setSessionFilter();
            }
        // End set

        $mhs = Sia::mahasiswa()
            ->select('m2.id as id_mhs_reg','m2.jenis_daftar','m2.nim','m2.id_prodi','p.nm_prodi', 'p.jenjang','jk.ket_keluar','m1.nm_mhs','m2.semester_mulai');
        // Filter angkatan
        if ( Session::get('tu_angkatan') != 'all' ) {
            $mhs->whereRaw('left('.Sia::prefix().'m2.nim,4)='.Session::get('tu_angkatan'));
        }

        // Filter smt masuk ganjil/genap
        if ( Session::get('tu_angkatan') != 'all') {
            $smtin = Session::get('tu_angkatan').Session::get('tu_smtin');
            if ( !empty(Session::get('tu_smtin')) && Session::get('tu_smtin') != 'all' ) {
                $mhs->where('m2.semester_mulai', $smtin);
            }
        }

        // Filter prodi
        if ( Session::get('tu_prodi') == 'all' ){
            $mhs->whereIn('m2.id_prodi', Sia::getProdiUser());
        } else {
            $mhs->where('m2.id_prodi', Session::get('tu_prodi'));
        }

        // Filter jns
        $mhs->where('m2.id_jenis_keluar', Session::get('tu_status'));

        // $mhs->where(function($q)use($r){
        //     $q->where('m2.id_jenis_keluar', 0)
        //         ->orWhere('m2.semester_keluar', '>=', Session::get('tu_smt'));
        // });

        $data['mahasiswa'] = $mhs->orderBy('m2.nim','desc')->get();

        $data['semester'] = Sia::listSemester();

        return view('tunggakan.index', $data);
    }

    private function setSessionFilter()
    {
        Session::set('tu_smt', Sia::sessionPeriode());
        Session::set('tu_status', '0');
        Session::set('tu_bayar', 'ALL');
        Session::set('tu_prodi', Sia::getProdiUser()[0]);
        Session::set('tu_angkatan', 'all');
        Session::set('tu_smtin', 'all');
    }

    public function cetak(Request $r)
    {

        $mhs = Sia::mahasiswa()
            ->select('m2.id as id_mhs_reg','m2.jenis_daftar','m2.nim','m2.id_prodi','p.nm_prodi', 'p.jenjang','jk.ket_keluar','m1.nm_mhs','m2.semester_mulai');
        // Filter angkatan
        if ( Session::get('tu_angkatan') != 'all' ) {
            $mhs->whereRaw('left('.Sia::prefix().'m2.nim,4)='.Session::get('tu_angkatan'));
        }

        // Filter smt masuk ganjil/genap
        if ( Session::get('tu_angkatan') != 'all') {
            $smtin = Session::get('tu_angkatan').Session::get('tu_smtin');
            if ( !empty(Session::get('tu_smtin')) && Session::get('tu_smtin') != 'all' ) {
                $mhs->where('m2.semester_mulai', $smtin);
            }
        }

        // Filter prodi
        if ( Session::get('tu_prodi') == 'all' ){
            $mhs->whereIn('m2.id_prodi', Sia::getProdiUser());
        } else {
            $mhs->where('m2.id_prodi', Session::get('tu_prodi'));
        }

        // Filter jns
        $mhs->where('m2.id_jenis_keluar', Session::get('tu_status'));

        if ( !empty($r->cari) ) {
            $mhs->where(function($q)use($r){
                $q->where('m2.nim', 'like', '%'.$r->cari.'%')
                    ->orWhere('m1.nm_mhs', 'like', '%'.$r->cari.'%');
            });
        }

        $mhs->where(function($q)use($r){
            $q->where('m2.id_jenis_keluar', 0)
                ->orWhere('m2.semester_keluar', '>=', Session::get('tu_smt'));
        });

        $data['mahasiswa'] = $mhs->orderBy('m2.nim','desc')->get();

        $data['semester'] = Sia::listSemester();

        return view('tunggakan.index', $data);
    }
}