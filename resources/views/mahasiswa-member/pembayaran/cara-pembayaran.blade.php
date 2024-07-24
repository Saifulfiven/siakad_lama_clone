<div class="panel-group" id="accordion-{{ $bri->id }}">

	<div class="panel panel-shadow">
	    <header class="panel-heading" style="padding:0 10px;font-weight: normal;padding: 0">
			<a data-toggle="collapse" style="color: #0aa699" data-parent="#accordion-{{ $bri->id }}" href="#{{ $bri->id }}-atm"><i class="collapse-caret fa fa-angle-up"></i> <i class="fa fa-thumb-tack"></i> ATM BRI</a>
	    </header>
	    <div id="{{ $bri->id }}-atm" class="panel-collapse collapse in">
			<div class="panel-body">
			   <ol class="show-list">
		            <li>Masukkan Kartu Debit BRI dan PIN Anda</li>
		            <li>Pilih menu Transaksi Lain &gt; Pembayaran &gt; Lainnya &gt; BRIVA</li>
		            <li>Masukkan Nomor <i>Virtual Account</i> anda <span class="amount">76266{{ $bri->cust_code }}</span></li>
		            <li>Di halaman konfirmasi, pastikan detil pembayaran sudah sesuai seperti Nomor BRIVA, Nama Pelanggan dan Jumlah Pembayaran</li>
		            <li>Ikuti instruksi untuk menyelesaikan transaksi</li>
		            <li>Simpan struk transaksi sebagai bukti pembayaran</li>
		        </ol>
			</div>
	    </div>
	</div>

	<div class="panel panel-shadow">
	    <header class="panel-heading" style="padding:0 10px;font-weight: normal;padding: 0">
			<a data-toggle="collapse" style="color: #0aa699" data-parent="#accordion-{{ $bri->id }}" href="#{{ $bri->id }}-mobile"><i class="collapse-caret fa fa-angle-up"></i> <i class="fa fa-thumb-tack"></i> Mobile Banking BRI</a>
	    </header>
	    <div id="{{ $bri->id }}-mobile" class="panel-collapse collapse">
			<div class="panel-body">
			   <ol class="show-list">
		            <li>Login aplikasi BRI Mobile</li>
		            <li>Pilih menu Mobile Banking BRI &gt; Pembayaran &gt; BRIVA</li>
		            <li>Masukkan Nomor <i>Virtual Account</i> anda <span class="amount">76266{{ $bri->cust_code }}</span></li>
		            <li>Masukan Jumlah Pembayaran</li>
		            <li>Masukkan PIN</li>
		            <li>Simpan notifikasi SMS sebagai bukti pembayaran</li>
		        </ol>
			</div>
	    </div>
	</div>

	<div class="panel panel-shadow">
	    <header class="panel-heading" style="padding:0 10px;font-weight: normal;padding: 0">
			<a data-toggle="collapse" style="color: #0aa699" data-parent="#accordion-{{ $bri->id }}" href="#{{ $bri->id }}-internet"><i class="collapse-caret fa fa-angle-up"></i> <i class="fa fa-thumb-tack"></i> Internet Banking BRI</a>
	    </header>
	    <div id="{{ $bri->id }}-internet" class="panel-collapse collapse">
			<div class="panel-body">
	        <ol class="show-list">
	            <li>Login pada alamat Internet Banking BRI (<a href="https://ib.bri.co.id/ib-bri/Login.html" target="_blank">https://ib.bri.co.id/ib-bri/Login.html</a>)</li>
	            <li>Pilih menu Pembayaran Tagihan &gt; Pembayaran &gt; BRIVA </li>
	            <li>Pada kolom kode bayar, Masukkan Nomor <i>Virtual Account</i> anda <span class="amount">76266{{ $bri->cust_code }}</span></li>
	            <li>Di halaman konfirmasi, pastikan detil pembayaran sudah sesuai seperti Nomor BRIVA, Nama Pelanggan dan Jumlah Pembayaran</li>
	            <li>Masukkan <span class="italic"> password</span> dan mToken</li>
	            <li>Cetak/simpan struk pembayaran BRIVA sebagai bukti pembayaran</li>
	        </ol>
			</div>
	    </div>
	</div>

	<div class="panel panel-shadow">
	    <header class="panel-heading" style="padding:0 10px;font-weight: normal;padding: 0">
			<a data-toggle="collapse" style="color: #0aa699" data-parent="#accordion-{{ $bri->id }}" href="#{{ $bri->id }}-mini-atm"><i class="collapse-caret fa fa-angle-up"></i> <i class="fa fa-thumb-tack"></i> Mini ATM/EDC BRI</a>
	    </header>
	    <div id="{{ $bri->id }}-mini-atm" class="panel-collapse collapse">
			<div class="panel-body">
		        <ol class="show-list">
		            <li>Pilih menu Mini ATM &gt; Pembayaran &gt; BRIVA</li>
		            <li> <span class="italic">Swipe</span> Kartu Debit BRI Anda </li>
		            <li>Masukkan Nomor <i>Virtual Account</i> anda <span class="amount">76266{{ $bri->cust_code }}</span></li>
		            <li>Masukkan PIN</li>
		            <li>Di halaman konfirmasi, pastikan detil pembayaran sudah sesuai seperti Nomor BRIVA, Nama Pelanggan dan Jumlah Pembayaran</li>
		            <li>Simpan struk transaksi sebagai bukti pembayaran</li>
		        </ol>
			</div>
	    </div>
	</div>

	<div class="panel panel-shadow">
	    <header class="panel-heading" style="padding:0 10px;font-weight: normal;padding: 0">
			<a data-toggle="collapse" style="color: #0aa699" data-parent="#accordion-{{ $bri->id }}" href="#{{ $bri->id }}-bank"><i class="collapse-caret fa fa-angle-up"></i> <i class="fa fa-thumb-tack"></i> Kantor Bank BRI</a>
	    </header>
	    <div id="{{ $bri->id }}-bank" class="panel-collapse collapse">
			<div class="panel-body">
		        <ol class="show-list">
		            <li>Ambil nomor antrian transaksi Teller dan isi slip setoran</li>
		            <li>Serahkan slip dan jumlah setoran kepada Teller BRI</li>
		            <li>Teller BRI akan melakukan validasi transaksi</li>
		            <li>Simpan slip setoran hasil validasi sebagai bukti pembayaran</li>
		        </ol>
			</div>
	    </div>
	</div>

	<div class="panel panel-shadow">
	    <header class="panel-heading" style="padding:0 10px;font-weight: normal;padding: 0">
			<a data-toggle="collapse" style="color: #0aa699" data-parent="#accordion-{{ $bri->id }}" href="#{{ $bri->id }}-atm-bank-lain"><i class="collapse-caret fa fa-angle-up"></i> <i class="fa fa-thumb-tack"></i> Bank Lainnya</a>
	    </header>
	    <div id="{{ $bri->id }}-atm-bank-lain" class="panel-collapse collapse">
			<div class="panel-body">
		        <ol class="show-list">
		           	<li>Masukkan Kartu Debit dan PIN Anda</li>
		            <li>Pilih menu Transaksi Lainnya &gt; Transfer &gt; Ke Rek Bank Lain</li>
		            <li>Masukkan kode bank BRI (002) kemudian diikuti dengan Nomor <i>Virtual Account</i> anda <span class="amount">76266{{ $bri->cust_code }}</span> </li>
		            <li>Ikuti instruksi untuk menyelesaikan transaksi</li>
		            <li>Simpan struk transaksi sebagai bukti pembayaran</li>
		        </ol>
			</div>
	    </div>
	</div>

</div>