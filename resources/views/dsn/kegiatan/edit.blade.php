{{ csrf_field() }}
<input type="hidden" name="id" value="{{ $kegiatan->id }}">
<div class="form-group">
    <label class="control-label">Kategori <span>*</span></label>
    <div>
        <select name="kategori" class="form-control">
            <option value="">Pilih Kategori</option>
            @foreach( Rmt::katKegiatanDosen() as $key => $val )
                <option value="{{ $key }}" {{ $kegiatan->id_kategori == $key ? 'selected':'' }}>{{ $val }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group">
    <label class="control-label">Nama Kegiatan <span>*</span></label>
    <div>
        <input type="text" name="nama_kegiatan" class="form-control" required="" value="{{ $kegiatan->nama_kegiatan }}">
    </div>
</div>

<div class="form-group">
    <label class="control-label">Tanggal Kegiatan <span>*</span></label>
    <div>
        <input type="date" name="tanggal_kegiatan" class="form-control mw-2" required="" value="{{ $kegiatan->tgl_kegiatan }}">
    </div>
</div>

<div class="form-group">
    <label class="control-label">Semester Akademik <span>*</span></label>
    <div>
        <select name="smt" class="form-control mw-2">
            @foreach( Sia::listSemester('filter') as $res )
                <option value="{{ $res->id_smt }}" {{ $kegiatan->smt == $res->id_smt ? 'selected':'' }}>{{ $res->nm_smt }}</option>
            @endforeach
        </select>
    </div>
</div>

@if ( !empty($kegiatan->file) )
    <div class="form-group">
        <label>File</label>
        <div>
            <?php 
                $icon = !empty($kegiatan->file) ? Rmt::icon($kegiatan->file) : '';
            ?>
            @if ( !empty($icon) )
                <div class="icon-resources">
                    <img width="24" src="{{ url('resources') }}/assets/img/icon/{{ $icon }}" />
                </div>

            @endif
             {{ $kegiatan->file }}
             <br>
             <br>
         </div>
    </div>
@endif

<div class="form-group">
    <label for="lampiran" >{{ empty($kegiatan->file) ? 'File' : 'Ganti File' }}</label>
    <input type="file" name="file" id="lampiran" class="form-control">
</div>

<div class="form-group" style="margin: 20px 0 30px -10px">

    <div>
        <button type="submit" id="btn-submit-edit" class="btn btn-theme btn-sm"><i class="fa fa-floppy-o"></i> Simpan</button> &nbsp; 
        <button type="reset" data-dismiss="modal" class="btn btn-sm"><i class="fa fa-times"></i>  Batal</button>
    </div>
</div>