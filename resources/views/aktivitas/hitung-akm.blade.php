@extends('layouts.app')

@section('title', 'Hitung Aktivitas Perkuliahan')
{{-- {{ dd($data) }} --}}
@section('content')
    <div id="overlay"></div>

    <div id="content">

        <div class="row">

            <div class="col-md-12">
                <section class="panel">
                    <header class="panel-heading">
                        Hitung Aktivitas Kuliah Mahasiswa TA. {{ Sia::sessionPeriode('nama') }}
                        <a href="{{ route('akm') }}" class="btn btn-success btn-xs pull-right">Kembali</a>
                    </header>


                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">

                                {{ Rmt::alertError() }}
                                {{ Rmt::alertSuccess() }}

                                <table border="0">
                                    <tr>
                                        <td width="100">Program Studi</td>
                                        <td>
                                            <select class="form-custom mw-2" id="filter-prodi">
                                                @foreach (Sia::listProdi() as $pr)
                                                    <option value="{{ $pr->id_prodi }}"
                                                        {{ Request::get('prodi') == $pr->id_prodi ? 'selected' : '' }}>
                                                        {{ $pr->jenjang }} {{ $pr->nm_prodi }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>


                                </table>

                                <br>

                                <div class="tabbable tab-xs">
                                    <ul class="nav nav-tabs" data-provide="tabdrop">
                                        {{-- {{ dd($angkatan_1) }} --}}
                                        @for ($i = $data['angkatan_1']; $i <= $data['max_angkatan']; $i++)
                                            <li{{ Request::get('angkatan') == $i ? ' class=active' : '' }}>
                                                <a style="padding: 5px 5px;font-size: 13px !important"
                                                    href="{{ route('akm_hitung') }}?prodi={{ Request::get('prodi') }}&angkatan={{ $i }}">{{ $i }}</a>
                                                </li>
                                        @endfor
                                    </ul>
                                    <div class="tab-content">

                                        <div class="tab-pane fade in active">

                                            <form action="{{ route('akm_store_arr') }}" method="post" id="form-status">

                                                {{ csrf_field() }}
                                                <input type="hidden" name="angkatan"
                                                    value="{{ Request::get('angkatan') }}">

                                                <button class="btn btn-primary btn-sm pull-right" id="submit"><i
                                                        class="fa fa-save"></i> SIMPAN</button>

                                                <div class="table-responsive">


                                                    <table cellpadding="0" cellspacing="0" border="0"
                                                        class="table table-bordered table-hover">
                                                        <thead class="custom">
                                                            <tr>
                                                                <th width="30px">No.</th>
                                                                <th>NIM</th>
                                                                <th>Nama Mahasiswa</th>
                                                                <th>Status Mahasiswa</th>
                                                                <th>Jml sks semester</th>
                                                                <th>Total sks</th>
                                                                <th>IPS</th>
                                                                <th>IPK</th>
                                                                <th width="20px">status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody align="center">
                                                            <?php $no = 1; ?>
                                                            @foreach ($data['mahasiswa'] as $m)
                                                                {{-- @if ($m->status_mbkm == null)
                                                                    <p>uiii uiii uiii uuiiii</p>
                                                                @endif --}}
                                                                <!-- Jika lulus di periode ini dan sks smt = 0 maka lewati Artinya aktivitas terakhirnya telah dilapor di periode sebelumnya -->
                                                                <?php if (empty($m->sks_smt) && $m->semester_keluar == Sia::sessionPeriode()) {
                                                                    continue;
                                                                } ?>

                                                                <?php $ipk = Sia::ipkAktivitas2($m->id); ?>

                                                                @if (!empty($m->mbkm) && $m->status_mbkm != null)
                                                                    <?php
                                                                    $total_sks = $m->sks_smt + $m->mbkm;
                                                                    $total_kum = $m->kumulatif1 + $m->kumulatif2;
                                                                    $total_ips = $total_kum / $total_sks;
                                                                    ?>
                                                                @else
                                                                    <?php
                                                                    $total_sks = $m->sks_smt;
                                                                    $total_ips = $m->ips;
                                                                    ?>
                                                                @endif

                                                                <input type="hidden" name="id_mhs_reg[]"
                                                                    value="{{ $m->id }}">
                                                                <input type="hidden" name="ips[]"
                                                                    value="{{ $total_ips }}">
                                                                <input type="hidden" name="sks_smt[]"
                                                                    value="{{ $total_sks }}">
                                                                <input type="hidden" name="ipk[]"
                                                                    value="{{ $ipk }}">
                                                                <input type="hidden" name="sks_total[]"
                                                                    value="{{ $m->sks_total >= 145 ? '145' : $m->sks_total }}">
                                                                <tr<?= empty($m->sks_smt) ? ' class=empty-sks' : '' ?>>
                                                                    <td>{{ $no++ }}</td>
                                                                    <td align="left">{{ $m->nim }}</td>
                                                                    <td align="left">{{ $m->nm_mhs }}</td>
                                                                    <td>
                                                                        <select name="status[]" class="form-control"
                                                                            style="width: 120px">
                                                                            @foreach (Sia::statusAkmMhs() as $res)
                                                                                <!-- jika akm sudah diinput sebelumnya -->
                                                                                @if (!empty($m->mbkm) && $m->status_mbkm != null)
                                                                                    <option value="K" selected>Kampus
                                                                                        Merdeka</option>
                                                                                @elseif (!empty($m->akm) && $m->sks_smt == 0 && $m->status_mbkm == null)
                                                                                    <option value="{{ $res->id_stat_mhs }}"
                                                                                        {{ $m->akm == $res->id_stat_mhs ? 'selected' : '' }}>
                                                                                        {{ $res->nm_stat_mhs }}</option>
                                                                                @else
                                                                                    <option value="{{ $res->id_stat_mhs }}"
                                                                                        {{ $m->sks_smt == 0 && $res->id_stat_mhs == 'N' ? 'selected' : '' }}>
                                                                                        {{ $res->nm_stat_mhs }}</option>
                                                                                @endif
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    @if (!empty($m->mbkm) && $m->status_mbkm != null)
                                                                        <td>{{ $m->sks_smt + $m->mbkm }}</td>
                                                                    @else
                                                                        <td>{{ $total_sks }}</td>
                                                                    @endif
                                                                    <td>{{ $m->sks_total >= 145 ? '145' : $m->sks_total }}
                                                                    </td>
                                                                    <td>{{ number_format($total_ips, 2) }}</td>
                                                                    <td>{{ $ipk }}</td>
                                                                    <td><?= !empty($m->akm) ? '<i class="fa fa-check" style="color:green"></i>' : '' ?>
                                                                    </td>
                                                                    </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                            </form>

                                        </div>
                                        <!-- //dosen -->

                                    </div>
                                    <!-- //tab-content -->
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

        </div>
        <!-- //content > row-->

    </div>
    <!-- //content-->

@endsection

@section('registerscript')
    <script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>

    <script>
        $(function() {
            $('#filter-prodi').change(function() {
                var prodi = $(this).val();
                var url = '?prodi=' + prodi + '&angkatan={{ Request::get('angkatan') }}';
                window.location.href = url;
            });

            $('#form-status').on('submit', function() {
                var btn = $('#submit');
                btn.attr('disabled', '');
                btn.html('<i class="fa fa-spinner fa-spin"></i> MENYIMPAN');
            });

            $('.empty-sks').css('background-color', 'rgb(245, 245, 158)');
        })
    </script>
@endsection
