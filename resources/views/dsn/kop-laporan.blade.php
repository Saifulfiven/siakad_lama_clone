<table width="100%" border="0">
	<tr>
		<td width="100"><img width="100%" src="{{ url('resources') }}/assets/img/logo.png"></td>
		<td><center>
				<h3><b>INSTITUTE TEKNOLOGI DAN BISNIS (ITB)<br>NOBEL INDONESIA</b></h3><br>
				{{ Sia::option('alamat_kampus') }}<br>
				{{ Sia::option('nomor') }}
			</center>
		</td>
		<td width="100">
			<img width="100%" src="{{ url('storage') }}/qr-code/{{ Sia::sessionDsn() }}.svg">
		</td>
	<tr>
</table>