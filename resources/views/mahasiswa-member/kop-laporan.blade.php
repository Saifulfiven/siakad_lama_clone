<table width="100%" border="0">
	<tr>
		<td width="100"><img width="100%" src="{{ url('resources') }}/assets/img/logo.jpg"></td>
		<td><center>
				<h3><b>SEKOLAH TINGGI ILMU EKONOMI (STIE)<br>NOBEL INDONESIA</b></h3><br>
				{{ Sia::option('alamat_kampus') }}<br>
				{{ Sia::option('nomor') }}
			</center>
		</td>
		<td width="100">
			<img width="100%" src="{{ url('storage') }}/qr-code/{{ Sia::sessionMhs('nim') }}.svg">
		</td>
	<tr>
</table>