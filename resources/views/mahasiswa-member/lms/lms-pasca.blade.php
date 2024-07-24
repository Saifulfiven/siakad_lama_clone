<div id="content" style="padding-top: 0">
    <div class="row" >

        <div class="col-md-12 learning">
            <div class="widget-chat">
                <header>
                    <span class="chat-collapse pull-right" title="Collapse chat">
                            <i class="fa fa-minus"></i>
                    </span>
                    <h4 class="online">
                        Materi Perkuliahan
                    </h4>
                </header>

                <div class="chat-body">
                    <?php 
                        $data = DB::table('materi_kuliah_pasca')
                                ->where('kode_mk', $r->kode_mk)
                                ->get();
                        
                    ?>

                    @if ( count($data) > 0 )

                        @foreach( $data as $res )

                            <a href="{{ route('mhs_lms_materi_pasca_download', ['id' => $res->id, 'file' => $res->file_materi]) }}" target="_blank" title="Download">

                                <div class="col-md-12 container-resources">
                                    <div class="icon-resources">
                                        <?php $icon = Rmt::icon($res->file_materi) ?>
                                        <img width="24" src="{{ url('resources') }}/assets/img/icon/{{ $icon }}" />
                                    </div>
                                    {{ $res->judul }}
                                </div>

                            </a>

                        @endforeach
                    
                    @else
                        Belum ada materi
                    @endif
                        
                </div>
            </div>
        </div>
    </div>
</div>