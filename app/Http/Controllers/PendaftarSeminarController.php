<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Seminar, App\Mahasiswareg, App\Pembayaran;
use DB, Sia, Rmt, Response, Session, Carbon;

class PendaftarSeminarController extends Controller
{

    public function index(Request $r)
    {
        if ( !Session::has('sem') ) {
            Session::put('sem.smt', Sia::sessionPeriode());
        }

        if ( $r->pencarian ) {
            if ( !empty($r->cari) ) {
                Session::put('sem.cari',$r->cari);
            } else {
                Session::pull('sem.cari');
            }

            return redirect()->route('seminar');
        }
        // dd(Session::get('sem'));

        $select_file = "(SELECT file from seminar_file
                            where id_seminar=sp.id and jenis_file='pembayaran' order by id desc limit 1) as file";

        $query = DB::table('seminar_pendaftaran as sp')
                    ->leftJoin('mahasiswa_reg as m1', 'sp.id_mhs_reg', 'm1.id')
                    ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->select('sp.*', 'm1.nim', 'm2.nm_mhs', DB::raw($select_file))
                    ->where('sp.id_smt', Session::get('sem.smt'))
                    ->whereIn('m1.id_prodi', Sia::getProdiUser());;

        $this->filter($query);

        $data['seminar'] = $query->orderBy('sp.created_at','desc')->paginate(20);

        return view('pendaftar-seminar.index', $data);
    }

    public function setFilter(Request $r)
    {
        if ( !empty($r->modul) ) {

            if ( $r->val == 'all' ) {
                Session::pull('sem.'.$r->modul);
            } else {
                Session::put('sem.'.$r->modul, $r->val);
            }
        }

        if ( $r->remove ) {
            Session::pull('sem');
        }

        return redirect(route('seminar'));
    }

    public function filter($query)
    {

        if ( Session::has('sem.jenis') ) {
            $query->where('sp.jenis', Session::get('sem.jenis'));
        }

        if ( Session::has('sem.status') ) {
            $query->where('sp.validasi_bauk', Session::get('sem.status'));
        }

        if ( Session::has('sem.cari') ) {
            $query->where(function($q) {
                $q->where('m1.nim','LIKE','%'.Session::get('sem.cari').'%')
                    ->orWhere('m2.nm_mhs', 'LIKE', '%'.Session::get('sem.cari').'%');
            });
        }

    }

    public function update(Request $r)
    {

        try {

            if ( $r->disetujui == 1 ) {
                
            }

            DB::beginTransaction();

            $mhs = Mahasiswareg::findOrFail($r->id_mhs_reg);

            // Dari database jenis_pembayaran
            $s1 = [
                'P' => 4,
                'H' => 5,
                'S' => 6
            ];

            $s2 = [
                'P' => 8,
                'H' => 9,
                'S' => 10
            ];

            $jenis_bayar = $mhs->id_prodi == 61101 ? $s2[$r->jenis] : $s1[$r->jenis];

            $jml_bayar = str_replace('.', '', $r->jumlah_bayar);
            $jml_bayar2 = str_replace(',', '', $jml_bayar);

            $seminar = Seminar::findOrFail($r->id);

            if ( $r->disetujui == 1 ) {

                // Store ke tabel pembayaran
                $this->storePembayaran($mhs->id, $jml_bayar2, $jenis_bayar);

                // Hanya update validasi bauk = 1 (diterima)
                DB::table('seminar_pendaftaran')->where('id', $seminar->id)
                    ->update(['validasi_bauk' => '1']);

            
            } else {

                // Pembayaran tidak valid
                DB::table('seminar_pendaftaran')->where('id', $seminar->id)
                    ->update(['validasi_bauk' => '0']);

            }

            DB::commit();

            Rmt::success('Berhasil mengupdate data');

        } catch( \Exception $e ) {

            DB::rollback();
            return Response::json([$e->getMessage()], 422);

        }
    }

    private function storePembayaran($id_mhs_reg, $jml_bayar, $id_jns_bayar)
    {

        try {

            // Cek apakah telah ada di table pembayaran
            $pembayaran = Pembayaran::where('id_mhs_reg', $id_mhs_reg)
                        ->where('id_jns_pembayaran', $id_jns_bayar)
                        ->where('id_smt', Session::get('sem.smt'))
                        ->first();

            if ( empty($pembayaran) ) {

                $data = new Pembayaran;
                $data->id_smt = Session::get('sem.smt');
                $data->id_mhs_reg = $id_mhs_reg;
                $data->tgl_bayar = Carbon::now();
                $data->jml_bayar = $jml_bayar;
                $data->jenis_bayar = 'LAINNYA';
                $data->id_jns_pembayaran = $id_jns_bayar;
                $data->ket = 'Insert from validasi bayar seminar';
                $data->save();

            } else {

                $data = Pembayaran::findOrFail($pembayaran->id);
                $data->jml_bayar = $jml_bayar;
                $data->ket = 'Update from validasi bayar seminar';
                $data->save();

            }

         } 
         catch(\Exception $e)
         {
            abort(422, $e->getMessage());
         }

    }

}