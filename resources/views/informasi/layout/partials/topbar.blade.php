<nav>
	<ul class="menu">
		<li><a href="#" id="toggle-menu"><i class="fa fa-bars"></i></a></li>
	</ul>
	<ul class="menu right">

		<li class="dropdown dropdown-right dropdown-profile">
			<a href="#">
				<div class="photo" style="background-image: url({{ url('/resources') }}/assets/img/avatar.png);"></div>
				{{ Auth::user()->nama }} <i class="fa fa-angle-down"></i>
			</a>
			<ul class="dropdown-menus">
				<li><a href="{{ url('beranda') }}">KE SIAKAD</a></li>
			</ul>
		</li>
	</ul>
	<div class="clearfix"></div>
</nav>