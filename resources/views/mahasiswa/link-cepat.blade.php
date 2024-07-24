<div class="col-md-3" style="padding-left: 0">

    <div class="table-responsive" style="padding-top: 35px">
        <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped">
            <thead class="custom">
                <tr><th style="padding: 5px 5px">Link cepat</th></tr>
            </thead>
            <tbody>
                <tr><td>
                    <div class="panel-group" id="accordion">
                        <div class="panel panel-info">
                            <div id="programStudi" class="panel-collapse collapse in">
                                <div class="panel-body" style="max-height: 700px;">
                                    @php
                                        if (!empty($data)) {
                                          # code...
                                          $id_mahasiswa = $data['id_mahasiswa'];
                                        }
                                    @endphp
                                    <a href="{{ route('mahasiswa_detail', ['id' => $id_mahasiswa]) }}">Detail mahasiswa</a><br>
                                    
                                    @if ( Sia::role('admin|akademik|jurusan|ketua 1'))
                                        <a href="{{ route('mahasiswa_regpd', ['id' => $id_mahasiswa]) }}">Histori pendidikan</a><br>
                                        @if( Sia::isTransfer($id_mahasiswa) )
                                            <a href="{{ route('mahasiswa_konfersi', ['id' => $id_mahasiswa]) }}">Nilai transfer</a><br>
                                        @endif
                                        <a href="{{ route('mahasiswa_krs', ['id' => $id_mahasiswa]) }}">KRS mahasiswa</a><br>
                                        <a href="{{ route('mahasiswa_nilai', ['id' => $id_mahasiswa]) }}">Histori nilai (KHS)</a><br>
                                        <a href="{{ route('mahasiswa_jdk', ['id' => $id_mahasiswa]) }}">Jadwal Perkuliahan</a><br>
                                        <a href="{{ route('mahasiswa_aktivitas', ['id' => $id_mahasiswa]) }}">Aktivitas perkuliahan</a><br>
                                        <a href="{{ route('mahasiswa_transkrip', ['id' => $id_mahasiswa]) }}">Transkrip & Ijazah</a><br>
                                    @endif

                                    @if ( Sia::role('admin|jurnal|akademik|cs') )
                                        <a href="{{ route('mahasiswa_jurnal', ['id' => $id_mahasiswa]) }}">Jurnal</a>
                                    @endif

                                    @if ( Sia::role('cs') )
                                        <br>
                                        <a href="{{ route('mahasiswa_nilai', ['id' => $id_mahasiswa]) }}">Histori nilai (KHS)</a><br>
                                        <a href="{{ route('mahasiswa_transkrip', ['id' => $id_mahasiswa]) }}">Transkrip & Ijazah</a>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                </td></tr>
            </tbody>
        </table>
    </div>

</div>