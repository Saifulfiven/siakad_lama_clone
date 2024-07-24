    <div id="modal-file" class="modal fade" data-width="700" tabindex="-1">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
            <h4 class="modal-title">File</h4>
        </div>
        <!-- //modal-header-->
        <div class="modal-body" style="padding: 0">
            <div class="tabbable tab-default">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#file" id="tab1" data-toggle="tab">Upload File</a></li>
                    <li><a href="#dokumen" data-toggle="tab">Ambil dari Dokumen saya</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="file">
                        <form action="{{ $formAction }}" enctype="multipart/form-data" method="post" class="dropzone" id="dropzone">
                            {{ csrf_field() }}
                            <div class="fallback">
                            </div>
                        </form>
                    </div>
                   <div class="tab-pane fade" id="dokumen">
                        <div class="alert alert-info">
                            Klik pada file untuk memilih
                        </div>
                        <?php
                            $id_dosen = Request::get('id_dosen');

                            $materi = DB::table('lms_bank_materi')
                                    ->where('id_dosen', $id_dosen)
                                    ->orderBy('id', 'desc')
                                    ->get();
                        ?>
                        @if ( count($materi) == 0 )
                            <p>Anda belum memimiliki file</p>
                        @endif

                        <div class="table-responsive list-materi">
                            <table class="table table-hover">
                            @foreach( $materi as $mt )
                                <?php $icon = Rmt::icon($mt->file) ?>
                                <tr onclick="pilih('{{ $mt->id }}', '{{ $mt->file }}', 'dokumen')">
                                    <td width="48">
                                        <img width="100%" src="{{ url('resources') }}/assets/img/icon/{{ $icon }}" />
                                    </td>
                                    <td class="judul">
                                        {{ $mt->file }}<br>
                                        <small>{{ Carbon::parse($mt->created_at)->format('d-m-Y H:i') }}</small>
                                    </td>
                                </tr>
                            @endforeach
                            </table>
                        </div>
                   </div>
                </div>
            </div>
        </div>
        <!-- //modal-body-->
     <!--    <div class="modal-footer">
            <center>
                <button type="button" data-dismiss="modal" class="btn btn-sm btn-default">Tutup</button>
            </center>
        </div> -->
    </div>