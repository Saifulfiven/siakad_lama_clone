@extends('layouts.auth-app')

@section('title', 'Reset Password - Siakad STIE Nobel Indonesia');

@section('konten')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Reset Password</div>
                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/email') }}">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                    
                                    @if ($errors->has('username'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Kirim Link Reset Password
                                    </button>
                                </div>
                            </div>
                        </form>

                        <hr>
                        <h4>Cara reset password?</h4>
                        <ol>
                            <li>Masukkan email yang terdaftar di siakad pada kolom email di atas kemudian klik tombol "Kirim Link Reset Password"</li>
                            <li>Tunggu hingga pengiriman link reset selesai</li>
                            <li>Setelah pengiriman link reset password selesai, masuk ke email anda dan buka pesan link reset password yang telah dikirimkan.</li> 
                            <li>Klik tombol 'Reset Password' yang ada pada pesan, kemudian anda akan diarahkan menuju halaman untuk membuat password baru.</li>
                            <li>Isikan form yang tersedia lalu klik tombol 'Reset Password'</li>
                        </ol>
                        <p><strong>Hubungi kami via email di <i>nobel@stienobel-indonesia.ac.id</i> jika terdapat kendala dalam aplikasi siakad. Email akan direspon dalam waktu 1 x 24 jam.</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection