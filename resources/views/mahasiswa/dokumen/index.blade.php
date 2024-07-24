<?php
$file = App\DokumenMhs::where('id_mhs', $mhs->id)
            ->orderBy('created_at', 'desc')
            ->get();
?>

    

<div class="table-responsive">
    <div style="width: 750px">
        <div class="col-md-12">
            <div class="pull-right">
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-add-dokumen"><i class="fa fa-plus"></i> Tambah</button>
            </div>
            <br>
            <hr>
        </div>

        <table cellpadding="0" cellspacing="0" border="0" style="width: 750px" class="table table-bordered table-striped table-hover" data-provide="data-table">
            <thead class="custom">
                <tr>
                    <th width="50">No.</th>
                    <th>Nama File</th>
                    <th width="200">Diupload Tgl</th>
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
                            <a href="{{ route('mahasiswa_doc_download', ['id' => $r->id, 'file' => $r->judul]) }}">
                                <span class="label bg-primary"> {{ Rmt::removeExtensi($r->file) }}</span>
                            </a>
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
