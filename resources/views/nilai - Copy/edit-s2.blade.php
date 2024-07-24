<?php
    $count_dsn = DB::table('dosen_mengajar')
                    ->where('id_jdk', $r->id)
                    ->count();

?>
<form action="{{ route('nil_update_s2') }}" method="post" id="form-nilai">

<input type="hidden" name="matakuliah" value="{{ $r->nm_mk }}">
<input type="hidden" name="id_smt" value="{{ $r->id_smt }}">
<input type="hidden" name="id_prodi" value="{{ $r->id_prodi }}">
<input type="hidden" name="jml_dosen" value="{{ $count_dsn }}">
<input type="hidden" name="dosen" value="{{ $r->dosen }}">

{{ csrf_field() }}
<div class="table-responsive">

    <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
        <thead class="custom">
            <tr>
                <th width="20px">No.</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Prodi</th>
                @if ( $count_dsn > 1 )
                    <th width="100">Dosen 1</th>
                    <th width="100">Dosen 2</th>
                @else
                    <th width="100">Dosen 1</th>
                @endif
                <th>Preview</th>
            </tr>
        </thead>
        <tbody align="center">
            @foreach( $peserta as $ps )
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td align="left">{{ $ps->nim }}</td>
                    <td align="left">{{ $ps->nm_mhs }}</td>
                    <td>{{ $ps->jenjang }} - {{ $ps->nm_prodi }}</td>
                    @if ( $count_dsn > 1 )
                        <td><input type="text" value="{{ $ps->nil_mid }}" onchange="hitung('{{ $ps->nim }}','uas')" id="uts-{{ $ps->nim }}" maxlength="5" name="uts[]" class="form-control number"></td>
                        <td><input type="text" value="{{ $ps->nil_final }}" onchange="hitung('{{ $ps->nim }}','uts')" id="uas-{{ $ps->nim }}" maxlength="5" name="uas[]" class="form-control number"></td>
                    @else
                        <td><input type="text" value="{{ $ps->nil_final }}" onchange="hitung('{{ $ps->nim }}','uas', this.value)" id="uas-{{ $ps->nim }}" maxlength="5" name="uas[]" class="form-control number"></td>
                    @endif
                    <td id="pre-{{ $ps->nim }}">
                        {{ empty($ps->nilai_huruf) ? '-': $ps->nilai_huruf }}
                    </td>
                    <input type="hidden" name="id_nilai[]" value="{{ $ps->id_nilai }}">
                    <input type="hidden" name="nil_lama[]" value="{{ $ps->nilai_huruf }}">
                    <input type="hidden" id="angka-{{ $ps->nim }}" name="nil_angka[]" value="{{ $ps->nilai_angka }}">
                    <input type="hidden" id="huruf-{{ $ps->nim }}" name="nil_huruf[]" value="{{ $ps->nilai_huruf }}">
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
</form>