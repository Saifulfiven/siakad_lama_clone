<ul class="nav navbar-nav nav-top-xs hidden-xs tooltip-area">
	<li class="h-seperate"></li>
	<li <?= Route::currentRouteName() == 'keu' ? 'style="background: #b3f5ef"':'' ?>><a href="{{ route('keu') }}">PEMBAYARAN SEMESTER</a></li>
	<li class="h-seperate"></li>
	<li <?= Route::currentRouteName() == 'keu_praktek' ? 'style="background: #b3f5ef"':'' ?>><a href="{{ route('keu_praktek') }}">PEMBAYARAN KULIAH PRAKTEK</a></li>
	<li class="h-seperate"></li>
	<li <?= Route::currentRouteName() == 'keu_sp' ? 'style="background: #b3f5ef"':'' ?>><a href="{{ route('keu_sp') }}">SP</a></li>
</ul>