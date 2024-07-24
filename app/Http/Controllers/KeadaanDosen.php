<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB, Sia;

trait KeadaanDosen
{
    public function KeadaanDosenIndex()
    {
        ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="custom">
                    <tr>
                        <th rowspan="2">NO</th>
                        <th rowspan="2">PRODI</th>
                        <th colspan="6">PANGKAT</th>
                        <th colspan="2">PENDIDIKAN TERAKHIR</th>
                    </tr>
                    <tr>
                        <th>Guru Besar</th>
                        <th>Lektor Kepala</th>
                        <th>Lektor</th>
                        <th>Asisten Ahli</th>
                        <th>Tenaga Pengajar</th>
                        <th>TOTAL</th>

                        <th>S2</th>
                        <th>S3</th>
                    </tr>
                </thead>
                <tbody align="center">
                    <?php 

                    $no = 1;
                    $tot_guru_besar = 0;
                    $tot_lektor_kpl = 0;
                    $tot_lektor = 0;
                    $tot_asisten = 0;
                    $tot_tenaga = 0;
                    $tot_s2 = 0;
                    $tot_s3 = 0;
                    $total_dosen = 0;

                    foreach( Sia::listProdi() as $pr ) { 
                        $guru_besar = $this->getJabatanDosen(4, $pr->id_prodi);
                        $lektor_kpl = $this->getJabatanDosen(3, $pr->id_prodi);
                        $lektor = $this->getJabatanDosen(2, $pr->id_prodi);
                        $asisten = $this->getJabatanDosen(1, $pr->id_prodi);
                        $tenaga = $this->getJabatanDosen(5, $pr->id_prodi);
                        $s2 = $this->lastPendidikan('S2', $pr->id_prodi);
                        $s3 = $this->lastPendidikan('S3', $pr->id_prodi);

                        $tot_dosen = $this->getTotalDosen($pr->id_prodi);

                        $tot_guru_besar += $guru_besar;
                        $tot_lektor_kpl += $lektor_kpl;
                        $tot_lektor += $lektor;
                        $tot_asisten += $asisten;
                        $tot_tenaga += $tenaga;
                        $tot_s2 += $s2;
                        $tot_s3 += $s3;

                        $total_dosen += $tot_dosen;
                    ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td align="left"><?= $pr->nm_prodi.' ('.$pr->jenjang.')' ?></td>

                            <td><?= $guru_besar ?></td>
                            <td><?= $lektor_kpl ?></td>
                            <td><?= $lektor ?></td>
                            <td><?= $asisten ?></td>
                            <td><?= $tenaga ?></td>
                            <td><b><?= $tot_dosen ?></b></td>

                            <td><?= $s2 ?></td>
                            <td><?= $s3 ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="2"><strong>TOTAL</strong></td>
                        <td style="font-weight: bold"><?= $tot_guru_besar ?></td>
                        <td style="font-weight: bold"><?= $tot_lektor_kpl ?></td>
                        <td style="font-weight: bold"><?= $tot_lektor ?></td>
                        <td style="font-weight: bold"><?= $tot_asisten ?></td>
                        <td style="font-weight: bold"><?= $tot_tenaga ?></td>

                        <td style="font-weight: bold"><?= $total_dosen ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br><br>
        <center>
            <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">Tutup</button>
        </center>
        <?php
    }

    private function getJabatanDosen($jabatan, $prodi)
    {
        $data = DB::table('dosen')
                ->where('id_prodi', $prodi)
                ->whereIn('jenis_dosen', ['DPK','DTY'])
                ->where('jabatan_fungsional', $jabatan)
                ->count();

        return $data;
    }

    private function lastPendidikan($pdk, $prodi)
    {
        $data = DB::table('dosen')
                ->where('id_prodi', $prodi)
                ->where('pendidikan_tertinggi', $pdk)
                ->whereIn('jenis_dosen', ['DPK','DTY'])
                ->count();

        return $data;
    }

    private function getTotalDosen($prodi)
    {
        $data = DB::table('dosen')
                ->where('id_prodi', $prodi)
                ->whereIn('jenis_dosen', ['DPK','DTY'])
                ->count();

        return $data;
    }
}