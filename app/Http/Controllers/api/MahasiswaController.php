<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Rmt, Response, Hash;
use App\Mahasiswareg;
use DB, Carbon;
use App\Imports\MahasiswaImport;
use Maatwebsite\Excel\Facades\Excel;
class MahasiswaController extends Controller
{
    use Library;

    public function __construct(Request $r)
    {
//        Rmt::auth(config('app.token'), $r->token);
    }

    public function cari(Request $r)
    {

        $mhs = DB::table('mahasiswa_reg as m1')
                    ->leftJoin('mahasiswa as m2', 'm1.id_mhs', 'm2.id')
                    ->select('m1.id', 'm1.nim', 'm2.nm_mhs')
                    ->where(function($q)use($r){
                        $q->where('nim','like', '%'.$r->cari.'%')
                            ->orWhere('nm_mhs', 'like', '%'.$r->cari.'%');
                    })->orderBy('m2.nm_mhs', 'asc')->take(10)->get();

        $data = ['mhs' => $mhs];

        $result = ['error' => 0, 'data' => $data];

        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);

    }

    public function jdkMahasiswa(Request $r)
    {
        $mhs = Mahasiswareg::where('nim', $r->nim)->first();

        if ( empty($mhs) ) {
            return Response::json(['error' => 1, 'msg' => 'Tidak ada bisa ditampilkan']);
        }

        $periode = $this->semesterBerjalan($mhs->id);
        $ta_aktif = empty($r->ta) || $r->ta == 'null' ? $periode['id'] : $r->ta;

        $jml_sks = 0;
        $tot_bobot = 0;
        $no = 1;
        $data_nilai = [];

        foreach( $this->nilai($mhs->id, $ta_aktif) as $n ) {
            
            $kumulatif = $n->sks_mk * $n->nilai_indeks;

            $data_nilai[]   = [
                'no' => $no++,
                'id_nilai' => $n->id,
                'matakuliah' => $n->nm_mk, 
                'sks' => $n->sks_mk, 
                'nilai' => $n->nilai_huruf
            ];

            $jml_sks += $n->sks_mk;
            $tot_bobot += $kumulatif;
        }

        if ( empty($tot_bobot) ) {
            $ips = '0.00';
        } else {
            $ips = round($tot_bobot/$jml_sks,2);
        }
        // Pembayaran
            $data = $this->mhsHistory($mhs->id, $ta_aktif, $mhs->semester_mulai, $mhs->id_prodi);

            if ( !empty($data['tagihan']) ) {
                $semester = DB::table('semester')
                            ->whereBetween('id_smt', [$mhs->semester_mulai, $ta_aktif])
                            ->orderBy('id_smt','desc')->get();

                $potongan = $this->totalPotonganPerMhs($mhs->id, $mhs->semester_mulai, $ta_aktif);

                $total_bayar = 0;
                $sisa_bayar = 0;
                $total_tagihan = 0;
                $all_potongan = [];
                $history_bayar = [];

                $posisi_semester = $this->posisiSemesterMhs($mhs->semester_mulai, $ta_aktif);

                if ( $posisi_semester > 1 ) {
                    
                    $total_tagihan = $data['tagihan']->bpp;

                } else {

                    $total_tagihan = $data['tagihan']->bpp + $data['tagihan']->spp + $data['tagihan']->seragam + $data['tagihan']->lainnya;
                }

                $total_tagihan = $total_tagihan - $potongan;

                $tagihan = [
                    [
                        'nama' => 'BPP',
                        'jml' => 'Rp '.Rmt::rupiah($data['tagihan']->bpp)
                    ],
                    [
                        'nama' => 'SPP',
                        'jml' => $posisi_semester == 1 ? 'Rp '.Rmt::rupiah($data['tagihan']->spp) : '-',
                    ],
                    [
                        'nama' => 'Seragam',
                        'jml' => $posisi_semester == 1 ? 'Rp '.Rmt::rupiah($data['tagihan']->seragam) : '-',
                    ],
                    [
                        'nama' => 'Lain-lain',
                        'jml' => !empty($data['tagihan']->lainnya) && $posisi_semester == 1 ? 'Rp '.Rmt::rupiah($data['tagihan']->lainnya) : '-',
                    ]
                ];

                if ( !empty($potongan) ) {
                        
                    $potong = \App\PotonganBiayaKuliah::where('id_mhs_reg', $mhs->id)->get();

                    foreach( $potong as $po ){
                        $ket = empty($po->ket) || $po->ket == '-' ? '':' ('.$po->ket.')';
                        $all_potongan[] = [
                            'nama' => $po->jenis_potongan.$ket,
                            'jml' => 'Rp '.Rmt::rupiah($po->potongan)
                        ];
                    }
                }


                $loop = 1;
                foreach( $data['pembayaran'] as $pmb ) {
                    $history_bayar[] = [
                        'no' => $loop++,
                        'tgl_bayar' => Carbon::parse($pmb->tgl_bayar)->format('d/m/Y'),
                        'jenis_bayar' => $pmb->jenis_bayar,
                        'jml' => Rmt::rupiah($pmb->jml_bayar)
                    ];

                    $total_bayar += $pmb->jml_bayar;
                }

                $sisa_bayar = $total_tagihan - $total_bayar;

            } else {
                $total_tagihan = 0;
                $tagihan    = [];
                $all_potongan   = [];
                $history_bayar  = [];
                $total_bayar    = 0;
                $sisa_bayar = 0;
            }

            $total_tagihan = empty($total_tagihan) ? '0' : 'Rp '.Rmt::rupiah($total_tagihan);
        // End pembayaran

        $result_nilai = [
            'count' => count($data_nilai), 
            'total_sks' => $jml_sks, 
            'ips' => $ips,
            'ipk' => $this->ipk($mhs->id, $mhs->semester_mulai, $ta_aktif),
            'data' => $data_nilai
        ];

        $result_ta =  $this->semester($mhs->semester_mulai, $periode['id'], $ta_aktif);

        $data_mhs = [
            'nim' => $mhs->nim,
            'nama' => $mhs->mhs->nm_mhs,
            'prodi' => $mhs->prodi->nm_prodi.' ('.$mhs->prodi->jenjang.')',
            'smstr' => $this->posisiSemesterMhs($mhs->semester_mulai, $ta_aktif)
        ];

        $data = [
            'ta' => $result_ta,
            'ta_aktif' => $ta_aktif,
            'nilai' => $result_nilai,
            'mhs' => $data_mhs,
            'total_tagihan' => $total_tagihan,
            'tagihan' => $tagihan,
            'all_potongan' => $all_potongan,
            'history_bayar' => $history_bayar,
            'total_bayar' => !empty($total_bayar) ? 'Rp '.Rmt::rupiah($total_bayar):0,
            'sisa_bayar' => !empty($sisa_bayar) ? 'Rp '.Rmt::rupiah($sisa_bayar):0,
        ];

        $result = ['error' => 0, 'data' => $data];
        // dd($result);
        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function mhsAbsen(Request $r)
    {
        $mhs = Mahasiswareg::where('nim', $r->nim)->first();
        $nilai = DB::table('nilai')->where('id', $r->id_nilai)->first();

        if ( empty($mhs) || empty($nilai) ) {
            return Response::json(['error' => 1, 'msg' => 'Tidak valid']);
        }

        $periode = $this->semesterBerjalan($mhs->id);
        $ta_aktif = empty($r->ta) || $r->ta == 'null' ? $periode['id'] : $r->ta;

        $data_mhs = [
            'nim' => $mhs->nim,
            'nama' => $mhs->mhs->nm_mhs
        ];

        $kehadiran = [
            [$nilai->a_1],
            [$nilai->a_2],
            [$nilai->a_3],
            [$nilai->a_4],
            [$nilai->a_5],
            [$nilai->a_6],
            [$nilai->a_7],
            [$nilai->a_8],
            [$nilai->a_9],
            [$nilai->a_10],
            [$nilai->a_11],
            [$nilai->a_12],
            [$nilai->a_13],
            [$nilai->a_14]
        ];

        $data = [
            'ta_aktif'  => $ta_aktif,
            'kehadiran' => $kehadiran,
            'mhs'       => $data_mhs
        ];

        $result = ['error' => 0, 'data' => $data];
        
        return Response::json($result, 200, [], JSON_UNESCAPED_SLASHES);

    }

    private function mhsHistory($id, $smt, $semester_mulai, $id_prodi)
    {

        $data['pembayaran'] = $this->historyBayar($smt)
                                ->where('p.id_mhs_reg', $id)
                                ->orderBy('p.tgl_bayar')->get();

        // $data['mhs'] = $this->mahasiswa()
        //                 ->select('m1.nm_mhs','p.jenjang','p.nm_prodi','m2.nim',
        //                     'm2.id as id_mhs_reg','m2.semester_mulai',
        //                     DB::raw('(select status_mhs from aktivitas_kuliah
        //                             where id_smt='.$smt.'
        //                             and id_mhs_reg=\''.$id.'\') as akm'))
        //                 ->where('m2.id', $id)
        //                 ->first();

        $data['tagihan'] = DB::table('biaya_kuliah')
                    ->where('tahun', substr($semester_mulai,0,4))
                    ->where('id_prodi', $id_prodi)
                    ->first();

        return $data;
    }

    private function mahasiswa()
    {
        $query = DB::table('mahasiswa_reg as m2')
            ->leftJoin('mahasiswa as m1','m1.id', '=', 'm2.id_mhs')
            ->leftJoin('agama as a', 'm1.id_agama','=','a.id_agama')
            ->leftJoin('prodi as p', 'p.id_prodi','=','m2.id_prodi')
            ->leftJoin('jenis_keluar as jk', 'jk.id_jns_keluar','=','m2.id_jenis_keluar');
        return $query;
    }

    public function import(Request $request){
        $this->validate($request,[
            'file' => 'required|file',
        ]);

        $file = $request->file('file');
        
        try {
            Excel::import(new MahasiswaImport(),$file);
            return redirect()->back()->with('success', 'File imported successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing the file. Please check the format and data.');
        }
        return redirect()->back()->with('success', 'File imported successfully.');
    }
}
