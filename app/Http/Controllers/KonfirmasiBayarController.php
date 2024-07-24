<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Rmt, Sia, DB, Response, Session;
use App\KonfirmasiBayar, App\Pembayaran;

class KonfirmasiBayarController extends Controller
{
    public function index(Request $r)
    {

        if ( !Session::has('konfir') ) {
            Session::put('konfir.smt', Sia::sessionPeriode());
        }

        $mhs = DB::table('konfirmasi_bayar as kb')
                ->join('konfirmasi_bayar_detail as kbd', 'kb.id', 'kbd.id_konfirmasi')
                ->join('jenis_pembayaran as jp', 'kbd.id_jns_pembayaran', 'jp.id_jns_pembayaran')
                ->join('mahasiswa_reg as m1', 'kb.id_mhs_reg', 'm1.id')
                ->join('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                ->select('kb.created_at','kbd.*','jp.id_jns_pembayaran','jp.ket','m1.nim','m1.id as id_mhs_reg','m2.nm_mhs');

        $this->filter($mhs);

        $data['mahasiswa'] = $mhs->orderBy('kb.created_at','desc')
                                ->paginate(10);

        return view('konfirmasi-bayar.index', $data);
    }

    private function filter($query)
    {
        if ( !Session::get('konfir.prodi') ){
            $query->whereIn('m1.id_prodi', Sia::getProdiUser());
        } else {
            $query->where('m1.id_prodi', Session::get('konfir.prodi'));
        }

        if ( Session::has('konfir.jenis_bayar') ) {
            $query->where('kbd.id_jns_pembayaran', Session::get('konfir.jenis_bayar'));
        }

        if ( Session::has('konfir.jenis_bayar') ) {
            $query->where('kbd.id_jns_pembayaran', Session::get('konfir.jenis_bayar'));
        }

        if ( Session::has('konfir.status') ) {
            $query->where('kbd.status', Session::get('konfir.status'));
        }

        if ( Session::has('konfir.smt') ) {
            $query->where('kb.id_smt', Session::get('konfir.smt'));
        }

        if ( Session::has('konfir.cari') ) {
            $query->where(function($q) {
                $q->where('m1.nim', 'LIKE', '%'.Session::get('konfir.cari').'%')
                    ->orWhere('m2.nm_mhs', 'LIKE', '%'.Session::get('konfir.cari').'%');
            });
        }
    }

    public function cari(Request $r)
    {
        if ( !empty($r->cari) ) {
            Session::put('konfir.cari',$r->cari);
        } else {
            Session::pull('konfir.cari');
        }

        return redirect(route('keu_konfir'));
    }

    public function setFilter(Request $r)
    {
        if ( !empty($r->modul) ) {

            if ( $r->val == 'all' ) {
                Session::pull('konfir.'.$r->modul);
            } else {
                if ( $r->modul == 'status' && $r->val == 99 ) {
                    Session::put('konfir.'.$r->modul,'0');
                } else {
                    Session::put('konfir.'.$r->modul,$r->val);
                }
            }
        }

        if ( $r->remove ) {
            Session::pull('konfir');
        }

        return redirect(route('keu_konfir'));
    }

    public function store(Request $r)
    {
        try {

            if ( $r->tolak ) {
                DB::table('konfirmasi_bayar_detail')->where('id', $r->id)
                    ->update(['status' => '2']);

                Rmt::success('Berhasil menyimpan perubahan');
                return redirect()->back();
            }

            if ( empty($r->jml_bayar) ) {
                return Response::json(['Jumlah bayar belum diisi'], 422);
            }

            DB::beginTransaction();

            $jml_bayar = str_replace('.', '', $r->jml_bayar);
            $jml_bayar2 = str_replace(',', '', $jml_bayar);

            // Set status disetujui
            DB::table('konfirmasi_bayar_detail')->where('id', $r->id)
                    ->update(['status' => '1']);

            // Insert pada table pembayaran
            $konfirmasi = DB::table('konfirmasi_bayar as kb')
                    ->join('konfirmasi_bayar_detail as kbd', 'kb.id', 'kbd.id_konfirmasi')
                    ->where('kbd.id', $r->id)
                    ->select('kb.id_mhs_reg','kb.created_at','kbd.*')
                    ->first();

            $this->pembayaran($konfirmasi, $jml_bayar2);

            DB::commit();

            Rmt::success('Berhasil menyimpan data');

        } catch ( \Exception $e ) {

            DB::rollback();
            return Response::json([$e->getMessage()], 422);

        }

    }

    private function pembayaran($kbd, $jml_bayar)
    {

        try {

            $data = new Pembayaran;
            $data->id_smt = Session::get('konfir.smt');
            $data->id_mhs_reg = $kbd->id_mhs_reg;
            $data->tgl_bayar = $kbd->created_at;
            $data->jml_bayar = $jml_bayar;
            $data->jenis_bayar = 'KONFIR';
            $data->id_jns_pembayaran = $kbd->id_jns_pembayaran;
            $data->save();

         } 
         catch(\Exception $e)
         {
            abort(422, $e->getMessage());
         }

    }

}
