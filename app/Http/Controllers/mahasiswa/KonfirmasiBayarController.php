<?php

namespace App\Http\Controllers\mahasiswa;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Rmt, Sia, DB, Response, Session;
use App\KonfirmasiBayar;

class KonfirmasiBayarController extends Controller
{
    public function index(Request $r)
    {
        $data['pembayaran'] = DB::table('konfirmasi_bayar as kb')
                                ->join('konfirmasi_bayar_detail as kbd', 'kb.id', 'kbd.id_konfirmasi')
                                ->join('jenis_pembayaran as jp', 'kbd.id_jns_pembayaran', 'jp.id_jns_pembayaran')
                                ->select('kb.created_at','kbd.*','jp.ket')
                                ->where('kb.id_mhs_reg', Sia::sessionMhs())
                                ->orderBy('kb.created_at','desc')
                                ->paginate(10);

        return view('mahasiswa-member.konfirmasi-bayar.index', $data);
    }

    public function store(Request $r)
    {
        $this->validate($r, [
            'file' => 'max:1024',
            'jenis_bayar' => 'required'
        ]);

        try {

            DB::beginTransaction();

            $id_mhs_reg = Sia::sessionMhs();

            $data = new KonfirmasiBayar;

            $fileName = '';

            if ( $r->hasFile('file') ) {

                $ekstArr = ['png','jpg','jpeg'];
                $ekstensi = $r->file->getClientOriginalExtension();

                if ( !in_array($ekstensi, $ekstArr) ) {
                    return Response::json(['Jenis file yang diperbolehkan adalah ('.implode(',', $ekstArr).')'], 422);
                }

                $nama_nim   = Sia::sessionMhs('nim') .'-'.str_slug(Sia::sessionMhs('nama'));

                $fileName   = 'Bukti Bayar '.$nama_nim.' ' .time().'.'.strtolower($ekstensi);
                $path   = config('app.konfirmasi-bayar');
                $upload = $r->file->move($path, $fileName);
            }

            $data->id_mhs_reg = $id_mhs_reg;
            $data->id_smt = Sia::sessionPeriode();
            $data->save();
            
            $id_konfir = $data->id;

            foreach( $r->jenis_bayar as $val ) {

                $this->cekKonfirmasi($id_mhs_reg, $val);

                $jenis = [
                    'id_konfirmasi' => $id_konfir,
                    'file' => $fileName,
                    'id_jns_pembayaran' => $val
                ];

                DB::table('konfirmasi_bayar_detail')
                    ->insert($jenis);
            }

            Rmt::success('Berhasil menyimpan data');

            DB::commit();

        } catch( \Exception $e ) {

            DB::rollback();
            return Response::json([$e->getMessage()], 422);

        }
    }

    private function cekKonfirmasi($id_mhs_reg, $jenis_bayar){

        // Cek apakah pembayaran yang dipilih belum ada di semester ini
        $cek = DB::table('konfirmasi_bayar as kb')
                    ->join('konfirmasi_bayar_detail as kbd', 'kb.id', 'kbd.id_konfirmasi')
                    ->where('kbd.id_jns_pembayaran', $jenis_bayar)
                    ->where('kb.id_mhs_reg', $id_mhs_reg)
                    ->count();

        if ( $cek > 0 ) {
            $jns_bayar = DB::table('jenis_pembayaran')
                            ->where('id_jns_pembayaran', $jenis_bayar)
                            ->first();

            abort(422, 'Pembayaran ini telah ada ('.$jns_bayar->ket.')');
        }

    }

    public function view($file)
    {
        echo '<img src="'.config('app.url-file').'/konfirmasi-pembayaran/'.$file.'">';
    }

    public function delete(Request $r)
    {
        try {

            DB::beginTransaction();

            $data = DB::table('konfirmasi_bayar_detail')
                    ->where('id', $r->id)->first();

            $id_konfir = $data->id_konfirmasi;
            $file = $data->file;

            DB::table('konfirmasi_bayar_detail')
                    ->where('id', $r->id)->delete();

            if ( !empty($file) ) {

                Rmt::unlink(config('app.konfirmasi-bayar').'/'.$file);
            }


            // Cek apakah masih data konfirmasi
            $cek = DB::table('konfirmasi_bayar_detail')
                    ->where('id_konfirmasi', $id_konfir)
                    ->count();

            if ( $cek == 0 ) {
                // Jika data konfirmasi detail kosong, delete konfirmasi
                $konfir = KonfirmasiBayar::find($id_konfir)->delete();

            }

            DB::commit();

            Rmt::success('Berhasil menghapus data');

            return redirect()->back();

        } catch( \Exception $e ) {

            DB::rollback();
            Rmt::error($e->getMessage());
            return redirect()->back();

        }
    }
}
