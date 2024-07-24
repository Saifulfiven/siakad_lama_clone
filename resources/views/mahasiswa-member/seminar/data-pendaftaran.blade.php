<?php 

$nama_seminar = Sia::jenisSeminar($jenis);

$id_mhs_reg = Sia::sessionMhs();

$ujian = DB::table('ujian_akhir')
            ->where('id_mhs_reg', $id_mhs_reg)
            ->where('jenis', $jenis)
            ->where('id_smt', Sia::sessionPeriode())
            ->first();

// dd($id_mhs_reg, Sia::sessionPeriode());
			
$pukul = !empty($ujian) ? explode(' - ', $ujian->pukul) : [];
if ( count($pukul) != 2 ) {
    $pukul = ['--', '--'];
}

$penguji = DB::table('penguji as p')
            ->leftJoin('dosen as d', 'p.id_dosen', 'd.id')
            ->select('p.*', DB::raw('concat_ws(\' \',d.gelar_depan,d.nm_dosen,d.gelar_belakang) as nm_dosen'),'p.nilai','d.ttd')
            ->where('p.id_mhs_reg', $seminar->id_mhs_reg)
            ->where('p.jenis', $seminar->jenis)
            ->where('p.id_smt', $seminar->id_smt)
            ->get();

// Persetujuan pembimbing & penguji
$persetujuan_pbb = true;

foreach( $penguji as $pg ) {

	if ( $pg->setuju != 1 ) {
		$persetujuan_pbb = false;
	}
}

// Validasi keuangan dan NDC (jika Hasil)
$validasi = false;

if ( $seminar->validasi_bauk == 1 ) {
	$validasi = true;
}

if ( $jenis == 'H' ) {
	if ( $seminar->validasi_ndc == 1 ) {
		$validasi = true;
	} else {
		$validasi = false;
	}
}


// Persetujuan pembimbing atau prodi (pers. prodi ditandai dengan telah diinputnya ruangan)
$setuju_pbb_prodi = false;

if ( !$persetujuan_pbb && empty($ujian->ruangan) ) {
	$setuju_pbb_prodi = false;
} else {
	$setuju_pbb_prodi = true;
}

if ( $setuju_pbb_prodi && $validasi ) { ?>

<div class="col-md-12">
    <div class="alert bg-success">
        <h4><strong><i class="fa fa-check-square"></i> {{ Sia::jenisSeminar($jenis) }} Telah Disetujui</strong></h4>
        <p>Seminar anda telah disetujui, silahkan lihat jadwal seminar yang fix pada tabel jadwal seminar di bawah.</p>
    </div>
</div>

<?php } ?>

<div class="clearfix"></div>

@include('mahasiswa-member.seminar.form-file')

<div class="clearfix"></div>

<div class="col-md-6">
    <br>
    <p>
        <strong>
            @if (!$setuju_pbb_prodi)
                Ajuan
            @endif
            Jadwal {{ $nama_seminar }}
        </strong>
    </p>

    @if ($penguji->count() < 3)

        <div class="alert alert-danger">
            Saat ini anda belum bisa mengajukan jadwal ujian/seminar karena penguji belum ditentukan oleh prodi.
            Sambil menunggu silahkan lengkapi berkas yang diperlukan di atas.
        </div>
    @else
        @if (empty($ujian->tgl_ujian))
            <div class="alert alert-info">
                Silahkan ajukan jadwal seminar yang diinginkan.
            </div>
        @endif

        <form action="{{ route('mhs_seminar_store_ajuan') }}" method="post" id="form-ajuan">

            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{ $ujian->id }}">
            @foreach ($penguji as $pg)
                <input type="hidden" name="penguji[]" value="{{ $pg->id_dosen }}">
            @endforeach

            <table class="table">
                <tr>
                    <td width="150">Hari / Tanggal</td>
                    <td width="20">:</td>
                    <td>
                        @if (!$setuju_pbb_prodi)
                            <input type="date" name="tanggal" class="form-control mw-2"
                                value="<?= empty($ujian->tgl_ujian) ? '' : $ujian->tgl_ujian ?>">
                        @else
                            {{ Rmt::tgl_indo($ujian->tgl_ujian) }}
                        @endif
                    </td>
                </tr>

                <tr>
                    <td>Pukul</td>
                    <td>:</td>
                    <td>
                        @if (!$setuju_pbb_prodi)
                            <select name="pukul_1" class="form-custom mw-2" onchange="getEnd(this.value)">
                                <option value="">--</option>
                                <?php foreach( Rmt::pukul() as $key => $val ) { ?>
                                <option value="<?= $key ?>" <?= $pukul[0] == $val ? 'selected' : '' ?>><?= $val ?>
                                </option>
                                <?php } ?>
                            </select>
                            &nbsp;<b>-</b>&nbsp;
                            <select name="pukul_2" class="form-custom mw-2" id="pukul-2">
                                <option value="">--</option>
                                <?php foreach( Rmt::pukul() as $key => $val ) { ?>
                                <option value="<?= $key ?>" <?= $pukul[1] == $val ? 'selected' : '' ?>><?= $val ?>
                                </option>
                                <?php } ?>
                            </select>
                        @else
                            {{ $pukul[0] }} - {{ $pukul[1] }} WITA
                        @endif

                    </td>
                </tr>
                <tr>
                    <td>Ruangan/Tempat</td>
                    <td>:</td>
                    <td>
                        @if (empty($ujian->ruangan))
                            Belum ditentukan oleh prodi
                        @else
                            {{ $ujian->ruangan }}
                        @endif
                    </td>
                </tr>
            </table>

            @if (empty($ujian->ruangan))
                <button class="btn btn-primary" id="btn-submit-ajuan" style="display: none"><i class="fa fa-save"></i>
                    Simpan</button>
            @endif
        </form>

    @endif

</div>


<div class="col-md-6">

    <br>
    <p><b>Validasi & Persetujuan Seminar</b></p>
    <table class="table table-striped table-hover">
        @foreach ($penguji as $pg)
            @if ($pg->id_dosen == Sia::sessionDsn())
                <?php $persetujuan_saya = $pg->setuju; ?>
                <?php $id_penguji = $pg->id; ?>
            @endif

            <tr>
                <td width="">{{ $pg->nm_dosen }}</td>
                <td>: {{ Rmt::status2($pg->setuju) }}</td>
            </tr>
        @endforeach

        <tr>
            <td>Validasi Pembayaran</td>
            <td>:
                {{ Rmt::status2($seminar->validasi_bauk) }}
            </td>
        </tr>
        @if ($jenis == 'H')
            <tr>
                <td>Validasi NDC</td>
                <td>:
                    {{ Rmt::status2($seminar->validasi_ndc) }}
                </td>
            </tr>
        @endif
    </table>

</div>

<script>
    @if (!$setuju_pbb_prodi)
        document.getElementById('btn-submit-ajuan').style.display = "block";
    @endif
</script>
