<div class="row">
    <div class="col-md-12">
        <br>
        <div class="alert-info" style="padding:10px;border-radius: none">
            Salin data Matakuliah Kurikulum dari &nbsp;
            @php
                $kurikulum = App\Kurikulum::where('id','<>',$kur->id)->where('id_prodi',$kur->id_prodi)->orderBy('nm_kurikulum','asc')->get(); 
            @endphp
            <select id="salin-mk" class="form-custom">
                <option value="">-- Pilih kurikulum --</option>
                @foreach( $kurikulum as $r )
                    <option value="{{ $r->id }}">{{ $r->nm_kurikulum }}</option>
                @endforeach
            </select>
            <button type="button" id="btn-salin-mk" class="btn btn-primary btn-sm">SALIN MATAKULIAH</button>

            <a href="{{ route('kurikulum_mk_add',['id' => $kur->id]) }}" class="btn btn-primary btn-sm">EDIT KOLEKTIF MATAKULIAH</a>
            <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-tambah" data-backdrop="static" data-keyboard="false" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> TAMBAH MATAKULIAH</a>
        </div>
        <br>
        <div class="table-responsive">
            <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover">
                <thead class="custom">
                    <tr>
                        <th width="40px">No</th>
                        <th width="120px">Kode Matakuliah</th>
                        <th align="left">Nama Matakuliah</th>
                        <th width="80px">SKS</th>
                        <th width="80px">Periode</th>
                        <th width="80px">Smstr</th>
                        <th width="80px">Jenis</th>
                        {{-- <th>Aksi</th> --}}
                    </tr>
                </thead>
                <tbody align="center">
                    <?php 
                        $total_sks = 0;
                        // dd($mk_kur);
                        // +
                         ?>

                    @foreach( $mk_kur as $r )
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td align="left">{{ $r->kode_mk }}</td>
                            <td align="left">{{ $r->nm_mk }}</td>
                            <td>{{ $r->sks_mk }}</td>
                            <td>{{ Sia::periode($r->periode) }}</td>
                            <td>{{ $r->smt }}</td>
                            <td>{{ Sia::jenisMatakuliah($r->jenis_mk) }}</td>
                            {{-- <td>
                                <span class="tooltip-area">
                                    <a href="{{ route('kurikulum_mk_delete',['id' => $r->id_mkur]) }}" onclick="return confirm('Anda ingin menghapus data ini?')" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-times"></i></a>
                                </span>
                            </td> --}}
                        </tr>
                        <?php $total_sks += $r->sks_mk ?>
                    @endforeach
                    <tr>
                        <td colspan="3"><b>TOTAL</b></td>
                        <td><b>{{ $total_sks }}</b></td>
                        <td colspan="4"></td>
                    </tr>
                </tbody>
            </table>
            @if ( count($mk_kur) < 1 )
                <center>Belum ada matakuliah</center>
            @endif
        </div>
    </div>
</div>

<div id="modal-tambah" class="modal fade" style="top:30%" data-width="600" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4>Matakuliah untuk kurikulum</h4>
    </div>
    <div class="modal-body">
        <form action="{{ route('kurikulum_mk_store') }}" id="form-matakuliah" method="post">
            {{ csrf_field() }}
            <input type="hidden" name="id_kurikulum" value="{{ $kur->id }}">
            <div class="table-responsive">
                <table border="0" class="table table-hover table-form">
                    <tr>
                        <td width="120px">Matakuliah<span>*</span></td>
                        <td>
                            <div class="input-icon right"> 
                                <span id="spinner-autocomplete" style="display: none"><i class="fa fa-spinner ico fa-spin"></i></span>
                                <input type="text" class="form-control" id="autocomplete-ajax">
                            </div>
                            
                            <input type="hidden" name="matakuliah" id="kode-mk">
                        </td>
                    </tr>
                    <tr>
                        <td>Semester<span>*</span></td>
                        <td>
                            <input type="number" class="form-control mw-1" name="semester">
                        </td>
                    </tr>
                </table>
            </div>
            <button type="submit" id="btn-submit" class="pull-right btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> SIMPAN</button>&nbsp; &nbsp; &nbsp;
        </form>
    </div>
</div>

<div id="modal-error" class="modal fade" tabindex="-1" style="top:30%" data-width="600">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h4 class="modal-title">Terjadi kesalahan</h4>
    </div>
    <!-- //modal-header-->
    <div class="modal-body">
        <div class="ajax-message-mk"></div>
        <hr>
        <center>
            <button type="button" data-dismiss="modal" class="btn btn-sm btn-danger">OK</button>
        </center>
    </div>
    <!-- //modal-body-->
</div>