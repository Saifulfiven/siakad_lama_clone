@extends('layouts.app')


@section('content')

<?php $alamat = 'https://ristekdikti.go.id/mahasiswa/'; ?>
<div id="content">
	<div style="background: url('assets/img/ring.svg') center center no-repeat; min-height: 100px">
			<iframe frameborder="0" 
				style="position: absolute; top:0;left:0;width:100%;height: 100%; border: none"
				src="{{ $alamat }}">		
			</iframe>
		</div>
</div>
<!-- //content-->
@endsection