<ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
	<li class="h-seperate"></li>
	<li <?= Request::get('jenis') == 'P' || Session::get('ua_jenis') == 'P' ? 'style="background: #b3f5ef"':'' ?>><a href="{{ route('ua') }}?jenis=P">Seminar Proposal</a></li>
	<li class="h-seperate"></li>
	<li <?= Request::get('jenis') == 'H' || Session::get('ua_jenis') == 'H' ? 'style="background: #b3f5ef"':'' ?>><a href="{{ route('ua') }}?jenis=H">Seminar Hasil</a></li>
	<li class="h-seperate"></li>
	<li <?= Request::get('jenis') == 'S' || Session::get('ua_jenis') == 'S' ? 'style="background: #b3f5ef"':'' ?>><a href="{{ route('ua') }}?jenis=S">Skripsi/Tesis/Disertasi</a></li>
</ul>