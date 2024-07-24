<?php

namespace App\Http\Controllers\api\tracerstudy;

use App\Http\Controllers\Controller;
use App\Mahasiswareg;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TracerStudyController extends Controller
{
    public function mahasiswaLulus(Request $request)
    {
        $mahasiswa = Mahasiswareg::with('mhs')->where('id_jenis_keluar', '1')
            ->where('nim', strtoupper($request->nim))
            ->first();

        if ($mahasiswa){
            $result['nama_mahasiswa'] = $mahasiswa->mhs['nm_mhs'];
            $result['email'] = $mahasiswa->mhs['email'];
            $result['nik'] = $mahasiswa->mhs['nik'];
            $result['npwp'] = $mahasiswa->mhs['npwp'];
            $result['hp'] = $mahasiswa->mhs['hp'];
            $result['id_prodi'] = $mahasiswa->id_prodi;
            $result['tahun_keluar'] = Carbon::parse($mahasiswa->tgl_keluar)->year;
            $result['nim'] = $mahasiswa->nim;
            return response()->json(['data' => $result], 200);
        }
        return response()->json(['data' => null], 404);
    }
}
