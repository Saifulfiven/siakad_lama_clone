@extends('layouts.app')

@section('title', 'Pengujian Skripsi/Tesis')

@section('content')
    <div id="overlay"></div>

    <div id="content">
        <div class="row">
            <div class="col-md-12">
                <section class="panel">
                    <header class="panel-heading">Pengujian Skripsi/Tesis</header>
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table border="0" width="100%" style="min-width: 550px;margin-bottom: 10px">
                                    <tr>
                                        <td width="160">
                                            <select class="form-control" style="max-width: 160px"
                                                onchange="filter('smt', this.value)">
                                                @foreach (Sia::listSemester() as $sm)
                                                    <option value="{{ $sm->id_smt }}"
                                                        {{ Session::get('pgj.smt') == $sm->id_smt ? 'selected' : '' }}>
                                                        {{ $sm->nm_smt }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td></td>
                                        {{-- <td width="250px">
                                            <form action="{{ route('dsn_pgj_cari') }}" method="post" id="form-cari">
                                                <div class="input-group pull-right">
                                                    {{ csrf_field() }}
                                                    <input type="text" class="form-control" name="cari"
                                                        value="{{ Session::get('bim.cari') }}">
                                                    <div class="input-group-btn">
                                                        <button class="btn btn-default" id="reset-cari" type="button"><i
                                                                class="fa fa-times"></i></button>
                                                        <button class="btn btn-primary"><i
                                                                class="fa fa-search"></i></button>
                                                    </div>
                                                </div>
                                            </form>
                                        </td> --}}
                                        <td width="120">
                                            <button onclick="openCetak()" class="btn btn-primary pull-right"><i
                                                    class="fa fa-print" disable></i> Cetak SK</button>
                                        </td>
                                    </tr>
                                </table>

                                <table class="table table-bordered table-striped table-hover" border="0" cellpadding="0"
                                    cellspacing="0">
                                    <thead class="custom">
                                        <tr>
                                            <th width="20px">No.</th>
                                            <th>NIM</th>
                                            <th>Nama</th>
                                            <th>Prodi</th>
                                            {{-- <th width="150">Aksi</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $i = 1;
                                        @endphp
                                        @foreach ($penguji as $p)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td align="left">{{ $p->nim }}</td>
                                                <td align="left">{{ $p->nm_mhs }}</td>
                                                <td align="center">{{ $p->jenjang }} - {{ $p->nm_prodi }}</td>
                                                {{-- <td align="center">
                                                <a href="#" class="btn btn-primary btn-sm" readonly>Detail</a>
                                                <a href="#" class="btn btn-secondary btn-sm"></a>
                                            </td> --}}
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <div id="modal-sk" class="modal fade md-stickTop" tabindex="-1">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                    class="fa fa-times"></i></button>
            <h4 class="modal-title">Pilih mahasiswa</h4>
        </div>
        <!-- //modal-header-->
        <div class="modal-body">
            <form action="{{ route('dsn_sk_pengujian') }}" target="_blank" method="post">
                {{ csrf_field() }}
                <div id="content-sk"></div>
            </form>
        </div>
        <!-- //modal-body-->
    </div>
@endsection

@section('heading')

@endsection

@section('registerscript')
    <script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
    <script>
        function openCetak() {
            $('#modal-sk').modal('show');
            $('#content-sk').html('<center><i class="fa fa-spinner fa-spin"></i></center>');
            $.ajax({
                url: '{{ route('dsn_sk_pengujian_data') }}',
                success: function(res) {
                    $('#content-sk').html(res);
                },
                error: function(err) {
                    alert('Gagal mengambil data, muat ulang halaman dan ulangi lagi');
                }
            });
        }

        function filter(module, value){
          window.location.href = '{{ route('dsn_pgj_filter') }}?modul=' + module + '&val=' + value;
        }
    </script>
@endsection
