@extends('layouts.app')

@section('title','File Manager')

@section('heading')
<script type="text/javascript" src="{{ url('resources') }}/assets/js/dropzone.js"></script>
<link href="{{ url('resources') }}/assets/css/dropzone.css" rel="stylesheet" />
@endsection


@section('content')

<div id="content">
  <div class="row">
    <div class="col-md-12">
      <section class="panel" style="padding-bottom: 50px">
        <header class="panel-heading">
            File Manager
            <div class="pull-right">
                <button class="btn btn-primary btn-xs" data-toggle="modal" data-target="#modal-add"><i class="fa fa-plus"></i> Tambah</button>
            </div>
        </header>
        
        <div class="panel-body" style="padding: 3px 3px;">

            <div class="col-md-12">
                
                {{ Rmt::AlertError() }}
                {{ Rmt::AlertSuccess() }}

                <div class="row">

                    <div class="col-md-12">

                        <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover" data-provide="data-table">
                            <thead class="custom">
                                <tr>
                                    <th width="50">No.</th>
                                    <th>Nama File</th>
                                    <th>Created At</th>
                                    <th width="80">Aksi</th>
                                </tr>
                            </thead>
                            <tbody align="center">
                                @foreach( $file as $r )
                                    <tr>
                                        <td width="38">{{ $loop->iteration }}</td>
                                        <td align="left">
                                            <div class="icon-resources">
                                                <?php $icon = Rmt::icon($r->file); ?>
                                                <img width="24" src="{{ url('resources') }}/assets/img/icon/{{ $icon }}" />
                                            </div>

                                            {{ Rmt::removeExtensi($r->file) }}
                                        </td>
                                        <td>{{ Carbon::parse($r->created_at)->format('d/m/Y h:i') }}</td>
                                        <td>
                                            <span class="tooltip-area">
                                                <a href="javascript:;" onclick="hapus('<?= $r->id ?>')" class="btn btn-theme btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>

            </div>

        </div>

    </div>
  </div>
</div>


<div id="modal-add" class="modal fade" data-width="700" style="top: 30%" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title">Tambahkan Bank Materi</h4>
    </div>

    <div class="modal-body" style="padding: 0">
        <div class="tabbable tab-default">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#file" id="tab1" data-toggle="tab">Upload File</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade in active" id="file">
                    <form action="{{ route('dsn_fm_store') }}" enctype="multipart/form-data" method="post" class="dropzone" id="dropzone">
                        {{ csrf_field() }}
                        <div class="fallback">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('registerscript')

<link href="{{ url('resources') }}/assets/plugins/bootstrap/bootstrap-editable.css" rel="stylesheet"/>
<script type="text/javascript" src="{{ url('resources') }}/assets/js/jquery.form.min.js"></script>
<!-- Library datable -->
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/datable/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="{{ url('resources') }}/assets/plugins/datable/dataTables.bootstrap.js"></script>
<script>

    Dropzone.options.dropzone = {
        maxFilesize: 16,
        dictDefaultMessage: 'Seret file ke sini atau klik',
        acceptedFiles: fileAccept(),
        success: function(file, response) 
        {
            location.reload();
        },
        error: function(file, response)
        {
            showMessage2('',response);
            this.removeAllFiles();
            return false;
        }
    };

    $(function(){
        $('table[data-provide="data-table"]').dataTable();

        $('.judul').editable({
            url: '{{ route('dsn_fm_update') }}',
            name: 'judul',
            params: function(params) {
                params._token = $('meta[name="csrf-token"]').attr('content');
                return params;
            },
            success: function(response, newValue) {
                showSuccess('Berhasil menyimpan data');
            },
            error: function(response,value)
            {
                console.log(JSON.stringify(response));
                var respon = parseObj(response.responseJSON);
                var pesan = '';
                for ( var i = 0; i < respon.length; i++ ){
                    pesan += "- "+respon[i]+"<br>";
                }
                if ( pesan == '' ) {
                    pesan = response.statusText;
                }
                showMessage2('', pesan);
            }
        });

    });

    function hapus(id)
    {
        if ( confirm('Anda ingin menghapus file ini ?') ) {
            window.location.href="{{ route('dsn_fm_delete') }}/"+id;
        }

    }
</script>
@endsection