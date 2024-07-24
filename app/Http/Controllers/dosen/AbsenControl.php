<?php

namespace App\Http\Controllers\dosen;

use Illuminate\Http\Request;

use DB, Sia, Rmt, Response, Session, Auth, Carbon;
use App\AbsenMhs;

trait AbsenControl
{

    public function absen(Request $r, $id)
    {
        $jenis = Session::has('jdm.jenis') ? Session::get('jdm.jenis') : $r->jenis;
        $query = Sia::jadwalKuliah('x', $jenis);

        $data['r'] = $query->where('jdk.id',$id)->first();

        $data['peserta'] = Sia::pesertaKelas($data['r']->id)->toArray();
        
        $data['id_jdk'] = $id;

        $data['jml_pertemuan'] = $r->jenis == 1 ? 14 : 10;

        $this->insertAbsenDosen($id, $data['r']->jam_masuk, $data['r']->jam_keluar);

        return view('dsn.absen.index', $data);
    }

    private function insertAbsenDosen($id_jdk, $jam_masuk, $jam_keluar)
    {
        $cek = DB::table('absen_dosen')
                ->where('id_jdk', $id_jdk)
                ->where('id_dosen', Sia::sessionDsn())
                ->count();

        if ( $cek == 0 ) {
            for ( $i = 1; $i <= 14; $i++ ) {
                $data = [
                    'id_dosen' => Sia::sessionDsn(),
                    'id_jdk' => $id_jdk,
                    'pertemuan' => $i,
                    'jam_masuk' => $jam_masuk,
                    'jam_keluar' => $jam_keluar,
                ];
                DB::table('absen_dosen')->insert($data);
            }
        }
    }

    public function absenStoreMhs(Request $r)
    {

        try {

            DB::table('nilai')->where('id', $r->nil)
                ->update(['a_'.$r->pertemuan => $r->abs]);

        } catch(\Exception $e) {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
        }

        return Response::json(['error' => 0,'msg' => ''], 200);
    }

    public function absenStoreDsn(Request $r)
    {

        try {

            DB::transaction(function()use($r){

                foreach( $r->id as $key => $val ) {
                    $masuk = !empty($r->bahasan[$key]) ? 1 : 0;
                    $tgl = empty($r->tanggal[$key]) ? NULL : \Carbon::parse($r->tanggal[$key])->format('Y-m-d');

                    $data = [
                        'masuk' => $masuk,
                        'tgl' => $tgl,
                        'jam_masuk' => $r->jam_masuk[$key],
                        'jam_keluar' => $r->jam_keluar[$key],
                        'pokok_bahasan' => $r->bahasan[$key]
                    ];

                    DB::table('absen_dosen')
                        ->where('id', $val)
                        ->update($data);
                }
            });

        } catch(\Exception $e) {
            return Response::json(['error' => 1, 'msg' => $e->getMessage()],200);
        }
        Rmt::success('Berhasil menyimpan data');

    }

    public function absenMhsCetak(Request $r, $id)
    {
        $jenis = Session::has('jdm.jenis') ? Session::get('jdm.jenis') : $r->jenis;

        $query = Sia::jadwalKuliah('x', $jenis);

        $data['r'] = $query->where('jdk.id',$id)->first();

        $data['peserta'] = Sia::pesertaKelas($data['r']->id)->toArray();

        // $this->insertAbsenDosen($id, $data['r']->jam_masuk, $data['r']->jam_keluar);
        

        $qr = 'ABSENSI KULIAH : '.Sia::sessionDsn('nama').','.$data['r']->nm_smt.' [STIE NOBEL INDONESIA]';
        \QrCode::generate($qr, storage_path().'/qr-code/'.Sia::sessionDsn().'.svg');
        
        $data['jml_pertemuan'] = $jenis == 2 ? 10 : 14;
        return view('dsn.absen.cetak-mhs', $data);
    }

    public function absenDsnCetak(Request $r, $id)
    {
        $jenis = Session::has('jdm.jenis') ? Session::get('jdm.jenis') : $r->jenis;
        $query = Sia::jadwalKuliah('x', $jenis);

        $data['r'] = $query->where('jdk.id',$id)->first();
        
        $data['absen'] = DB::table('absen_dosen')
                    ->where('id_jdk', $id)
                    ->where('id_dosen', Sia::sessionDsn())
                    ->orderBy('pertemuan')
                    ->orderBy('id')
                    ->groupBy('pertemuan')
                    ->get();

        $qr = 'BERITA ACARA PERKULIAHAN : '.Sia::sessionDsn('nama').','.$data['r']->nm_smt.' [STIE NOBEL INDONESIA]';
        \QrCode::generate($qr, storage_path().'/qr-code/'.Sia::sessionDsn().'.svg');

        return view('dsn.absen.cetak-dsn', $data);
    }

    public function absenBuka(Request $r)
    {
        $this->validate($r, [
            'pertemuan' => 'required',
            'waktu' => 'required|numeric',
        ]);

        // Cek apakah sudah ada pertemuan tersebut
        $cek = AbsenMhs::where('id_jdk', $r->id_jdk)
                ->where('pertemuan_ke', $r->pertemuan)
                ->count();

        if ( $cek > 0 ) {
            return Response::json(['Pertemuan ini telah dibuka sebelumnya'], 422);
        }

        // waktu < 5 menit
        if ( $r->waktu < 5 ) {
            return Response::json(['Waktu absen tidak boleh kurang dari 5 menit'], 422);
        }


        $now = Carbon::now();
        $end_time = Carbon::now()->addMinutes($r->waktu);

        $data = new AbsenMhs;
        $data->id_jdk = $r->id_jdk;
        $data->pertemuan_ke = $r->pertemuan;
        $data->waktu = $r->waktu;
        $data->created_at = $now;
        $data->updated_at = $end_time;
        $data->save();

        Rmt::success('Berhasil membuka absen');

    }
}