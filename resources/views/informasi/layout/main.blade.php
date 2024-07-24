<!DOCTYPE html>
<html lang="en">
<head>
	@include('informasi.layout.partials.meta')
	<title>@yield('title', 'Absensi')</title>
	@include('informasi.layout.partials.styles')
	@yield('styles')
</head>
<body>

	@include('informasi.layout.partials.header')

	<section class="main">
		
		@include('informasi.layout.partials.topbar')

		@yield('contents')

		@include('informasi.layout.partials.footer')
		
	</section>

	@include('informasi.layout.partials.modal')

	@yield('modal')

	@include('informasi.layout.partials.script')

	@yield('registerscript')

</body>
</html>