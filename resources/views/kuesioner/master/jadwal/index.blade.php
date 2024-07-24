@extends('layouts.app')

@section('title','Kuesioner')

@section('content')

    <div id="overlay"></div>

    <div id="content">
    
        <div class="row">
                
            <div class="col-md-12">
                <section class="panel">
                    <header class="panel-heading">
                        Kuesioner
                    </header>

                    <div class="panel-body">

                        <div class="col-md-8">

                            {{ Rmt::AlertSuccess() }}
                            {{ Rmt::AlertError() }}

                            Filter Prodi : 
                            <select class="custom" onchange="filterProdi(this.value)">
                                <option value="">Semua</option>
                                @foreach( Sia::listProdi() as $pr )
                                    <option value="{{ $pr->id_prodi }}" {{ Request::get('prodi') == $pr->id_prodi ? 'selected':'' }}>{{ $pr->jenjang.' '.$pr->nm_prodi }}</option>
                                @endforeach
                            </select>
                            
                            <div class="table-responsive">
                                <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                                    <thead class="custom">
                                        <tr>
                                            <th width="20px">No.</th>
                                            <th>TA</th>
                                            <th width="150px">Prodi</th>
                                            <th>Ket</th>
                                            <th>Aktif</th>
                                            <th width="80px">Tools</th>
                                        </tr>
                                    </thead>
                                    <tbody align="center">
                                        @foreach( $kuesioner as $r )
                                            <tr>
                                                <td>{{ $loop->iteration - 1 + $kuesioner->firstItem() }}</td>
                                                <td>{{ $r->id_smt }}</td>
                                                <td align="left">{{ $r->jenjang.' '.$r->nm_prodi }}</td>
                                                <td align="left">{{ $r->ket }}</td>
                                                <td>{{ $r->aktif == 1 ? 'AKTIF':'NON AKTIF' }}</td>
                                                <td>
                                                    <span class="tooltip-area">
                                                        <a href="javascript::void()" 
                                                            data-id="{{ $r->id }}"
                                                            data-prodi="{{ $r->id_prodi }}"
                                                            data-ket="{{ $r->ket }}" 
                                                            data-ta="{{ $r->id_smt }}" 
                                                            data-aktif="{{ $r->aktif }}"
                                                            class="btn btn-warning btn-xs ubah" title="Ubah"><i class="fa fa-pencil"></i></a> &nbsp; &nbsp; 
                                                        <a href="{{ route('kues_jadwal_delete', ['id' => $r->id]) }}" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            {{ count($kuesioner) == 0 ? 'Belum ada data':'' }}

                            {!! $kuesioner->appends(array_except(Request::query(),'page'))
                                    ->links() !!}
                        </div>

                        <div class="col-md-4">

                            <h4 id="title-form">Tambah Kuesioner</h4>
                            <br>
                            {{ Rmt::alertErrors($errors) }}

                            <form action="{{ route('kues_jadwal_store') }}" id="form" class="form-horizontal" method="post" data-collabel="3" data-alignlabel="left">
                                {{ csrf_field() }}
                                <input type="hidden" name="id" id="id-kuesioner">
                                
                                <label class="control-label">Tahun Akademik <span>*</span></label>
                                <input type="text" value="{{ Sia::sessionPeriode() }}" class="form-control" id="ta" name="ta" value="{{ old('ta') }}" required="">

                                <label class="control-label">Ket <span>*</span></label>
                                <select name="ket" class="form-control" id="ket">
                                    <option value="MID">MID</option>
                                    <option value="FINAL">FINAL</option>
                                </select>

                                <label class="control-label">Program Studi <span>*</span></label>
                                <select name="id_prodi" class="form-control" id="prodi">
                                    <option value="">--Pilih Program Studi--</option>
                                    @foreach( Sia::listProdi() as $pr )
                                        <option value="{{ $pr->id_prodi }}">{{ $pr->jenjang.' '.$pr->nm_prodi }}</option>
                                    @endforeach
                                </select>

                                <label class="control-label">Status <span>*</span></label>
                                <select name="aktif" class="form-control" id="aktif">
                                    <option value="1">AKTIF</option>
                                    <option value="0">NON AKTIF</option>
                                </select>

                                <button class="btn btn-primary btn-xs pull-right" id="btn-submit" style="margin: 6px 0px"><i class="fa fa-floppy-o"></i> SIMPAN</button>
                                <button type="button" class="btn btn-warning btn-xs pull-left" id="btn-cancel" style="margin: 6px 0px;display:none"><i class="fa fa-times"></i> BATAL</button>
                            </form>
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
<script>
    $('.ubah').click(function(){
        var div = $(this);
        $('#ket').val(div.data('ket'));
        $('#ta').val(div.data('ta'));
        $('#id-kuesioner').val(div.data('id'));
        $('#prodi').val(div.data('prodi'));
        $('#aktif').val(div.data('aktif'));
        $('#title-form').html('Ubah Kuesioner');
        $('#btn-cancel').show();
        $('#form').attr('action','{{ route('kues_jadwal_update') }}');
    });

    $('#btn-cancel').click(function(){
        $('#ket').val('');
        $('#title-form').html('Tambah Kuesioner');
        $('#form').attr('action','{{ route('kues_jadwal_store') }}');
        $('#btn-cancel').hide();
    });

    function filterProdi(prodi)
    {
        window.location.href = '?prodi='+prodi;
    }
</script>
@endsection