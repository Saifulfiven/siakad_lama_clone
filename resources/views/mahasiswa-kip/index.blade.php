@extends('layouts.app')

@section('title', 'Data Mahasiswa KIP')

@section('heading')
    <link type="text/css" rel="stylesheet" src="https://cdn.datatables.net/1.13.2/css/dataTables.bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        div.dataTables_filter {
            margin-bottom: 0 !important;
            float: right;
        }

        div.dataTables_paginate {
            margin: 10px 0;
            font-size: 12px;
            float: right;
        }
    </style>
@endsection

@section('topMenu')
    <ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
        <li class="h-seperate"></li>
        <li><a>DATA BEASISWA</a></li>
    </ul>
@endsection

@section('content')
    <div id="content">
        <div class="row">
            <div class="col-md">
                <div class="panel">
                    <div class="panel-body">
                        {{-- <a href="{{ route('ctkMhsKip') }}" class="btn btn-primary" style="float: right;">
                            Export PDF
                        </a> --}}
                    </div>
                </div>
            </div>
            <div class="col-md mt-4">
                <div class="panel">
                    <div class="panel-body">
                        {{-- <div class="table-responsive"> --}}
                        <table class="table table-bordered table-responsive table striped table-hover" id="mhsKip">
                            <thead class="custome">
                                <tr>
                                    <th>No.</th>
                                    <th>NIM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Prodi</th>
                                    <th>Angkatan</th>
                                    {{-- <th>Action</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 1;
                                @endphp
                                @foreach ($data as $bs)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $bs->nim }}</td>
                                        <td>{{ $bs->nm_mhs }}</td>
                                        <td>
                                            @if ($bs->id_prodi == '62201')
                                                Akuntansi (S1)
                                            @elseif ($bs->id_prodi == '61201')
                                                Manajemen (S1)
                                            @elseif ($bs->id_prodi == '83207')
                                                Pendidikan Teknologi Informasi (S1)
                                            @elseif ($bs->id_prodi == '59201')
                                                Sistem dan Teknologi Informasi (S1)
                                            @elseif ($bs->id_prodi == '61112')
                                                Keuangan Publik (S2)
                                            @elseif ($bs->id_prodi == '61101')
                                                Manajemen (S2)
                                            @endif
                                        </td>
                                        <td>{{ substr($bs->semester_mulai, 0, 4) }}</td>
                                        {{-- <td>
                                        <a href="" class="btn btn-primary">Test</a>
                                    </td> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{-- </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('registerscript')
    {{-- <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script> --}}
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.2/js/dataTables.bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#mhsKip').DataTable();
        });
    </script>
@endsection
