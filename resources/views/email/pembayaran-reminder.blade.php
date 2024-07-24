<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <style type="text/css" rel="stylesheet" media="all">
        /* Media Queries */
        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>
</head>

<?php

$style = [
    /* Layout ------------------------------ */

    'body' => 'margin: 0; padding: 0; width: 100%;',
    'email-wrapper' => 'width: 100%; margin: 0; padding: 0; background-color: #FFF;',
    'kontainer' => 'border: 1px solid #EDEFF2;width: auto;max-width: 570px;margin: 0 auto;padding: 5px;',
    /* Masthead ----------------------- */

    'email-body' => 'width: 100%; margin: 0; padding: 0; background-color: #FFF;',
    'email-body_inner' => 'width: auto; max-width: 570px; margin: 0 auto; padding: 0;',
    'email-body_cell' => 'padding: 5px;',

    'email-footer' => 'margin: 0 auto; padding: 0; text-align: center;background-color: #EDEFF2',
    'email-footer_cell' => 'color: #AEAEAE; padding: 0px; text-align: center;',

    /* Body ------------------------------ */

    'body_action' => 'width: 100%; margin: 30px auto; padding: 0; text-align: center;',
    'body_sub' => 'margin-top: 25px; padding-top: 25px; border-top: 1px solid #EDEFF2;',
    'table' => 'border: 1px solid #EDEDED',
    /* Type ------------------------------ */

    'anchor' => 'color: #e74c3c;text-decoration:none',
    'header-1' => 'margin-top: 0; color: #2F3133; font-size: 19px; font-weight: bold; text-align: left;',
    'paragraph' => 'margin-top: 0; color: #74787E; font-size: 16px; line-height: 1.5em;',
    'paragraph-sub' => 'color: #74787E; font-size: 12px; line-height: 1.5em;',
    'paragraph-center' => 'text-align: center;',

    /* Buttons ------------------------------ */

    'button' => 'display: block; display: inline-block; width: 200px; min-height: 20px; padding: 10px;
                 background-color: #3869D4; border-radius: 3px; color: #ffffff; font-size: 15px; line-height: 25px;
                 text-align: center; text-decoration: none; -webkit-text-size-adjust: none;',

    'button--green' => 'background-color: #22BC66;',
    'button--red' => 'background-color: #dc4d2f;',
    'button--blue' => 'background-color: #3869D4;',
    'h5' => 'font-size: 14px;margin: 5px',
    'ol' => 'line-height: 2em;padding-left: 25px'
];
?>

<?php $fontFamily = 'font-family: Arial, \'Helvetica Neue\', Helvetica, sans-serif;'; ?>

<body style="{{ $style['body'] }}">
    <div style="{{ $style['kontainer'] }}">
        <table width="100%" cellpadding="0" cellspacing="0" style="{{ $style['table'] }}">
            <tr>
                <td style="{{ $style['email-wrapper'] }}" align="center">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <!-- Email Body -->
                        <tr>
                            <td style="{{ $style['email-body'] }}" width="100%">
                                <table style="{{ $style['email-body_inner'] }}" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="{{ $fontFamily }} {{ $style['email-body_cell'] }}">
                                           	<p style="border-bottom: 1px solid #74787E"><img src="http://siakad.stienobel-indonesia.ac.id/resources/assets/img/brand-merah-hitam.png"/></p>
                                            <!-- Greeting -->
                                            <h1 style="{{ $style['header-1'] }}">
                                                {{ $header }}
                                            </h1>

                                            <!-- Intro -->
                                            <p style="{{ $style['paragraph'] }}">
                                                {{ $msg }}
                                            </p>
                                        </td>
                                    </tr>
                                </table>

                                <table border="0" style="{{ $fontFamily }};margin: 0 auto; width: auto;max-width: 560px;padding: 5px">
                                    <tr>
                                        <td width="170">
                                            <small>Total Pembayaran</small>
                                            <p style="{{ $style['paragraph'] }}">{{ $amount }}</p>
                                        </td>
                                        <td>
                                            <small>Batas Waktu pembayaran</small>
                                            <p style="{{ $style['paragraph'] }}">{{ $exp_date }}</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="150">
                                            <small>Metode Pembayaran</small>
                                            <img width="90px" style="margin-top: 5px" src="http://siakad.stienobel-indonesia.ac.id/resources/assets/img/bri.png"/>
                                            <p style="{{ $style['paragraph'] }}">BRI Virtual Account</p>
                                        </td>
                                        <td valign="top">
                                            <small>No. Virtual Account</small>
                                            <p style="{{ $style['paragraph'] }}">{{ $cust_code }}</p>
                                        </td>
                                    </tr>
                                </table>

                                <table style="margin: 0 auto; width: auto; max-width: 560px;padding: 5px" width="100%">
                                    <tr>
                                        <td style="{{ $fontFamily }};font-size: 13px">
                                            <!-- Cara bayar -->
                                                <p style="{{ $style['paragraph'] }}">Cara Pembayaran</p>
                                                <h5 style="{{ $style['h5'] }}">ATM BRI</h5>
                                                <ol style="{{ $style['ol'] }}">
                                                    <li>Masukkan Kartu Debit BRI dan PIN Anda</li>
                                                    <li>Pilih menu Transaksi Lain &gt; Pembayaran &gt; Lainnya &gt; BRIVA</li>
                                                    <li>Masukkan Nomor <i>Virtual Account</i> anda <span class="amount">{{ $cust_code }}</span></li>
                                                    <li>Di halaman konfirmasi, pastikan detil pembayaran sudah sesuai seperti Nomor BRIVA, Nama Pelanggan dan Jumlah Pembayaran</li>
                                                    <li>Ikuti instruksi untuk menyelesaikan transaksi</li>
                                                    <li>Simpan struk transaksi sebagai bukti pembayaran</li>
                                                </ol>

                                                <h5 style="{{ $style['h5'] }}">Mobile Banking BRI</h5>
                                                <ol style="{{ $style['ol'] }}">
                                                    <li>Login aplikasi BRI Mobile</li>
                                                    <li>Pilih menu Mobile Banking BRI &gt; Pembayaran &gt; BRIVA</li>
                                                    <li>Masukkan Nomor <i>Virtual Account</i> anda <span class="amount">{{ $cust_code }}</span></li>
                                                    <li>Masukan Jumlah Pembayaran</li>
                                                    <li>Masukkan PIN</li>
                                                    <li>Simpan notifikasi SMS sebagai bukti pembayaran</li>
                                                </ol>

                                                <h5 style="{{ $style['h5'] }}">Internet Banking BRI</h5>
                                                <ol style="{{ $style['ol'] }}">
                                                    <li>Login pada alamat Internet Banking BRI (<a href="https://ib.bri.co.id/ib-bri/Login.html" target="_blank">https://ib.bri.co.id/ib-bri/Login.html</a>)</li>
                                                    <li>Pilih menu Pembayaran Tagihan &gt; Pembayaran &gt; BRIVA </li>
                                                    <li>Pada kolom kode bayar, Masukkan Nomor <i>Virtual Account</i> anda <span class="amount">{{ $cust_code }}</span></li>
                                                    <li>Di halaman konfirmasi, pastikan detil pembayaran sudah sesuai seperti Nomor BRIVA, Nama Pelanggan dan Jumlah Pembayaran</li>
                                                    <li>Masukkan <span class="italic"> password</span> dan mToken</li>
                                                    <li>Cetak/simpan struk pembayaran BRIVA sebagai bukti pembayaran</li>
                                                </ol>

                                                <h5 style="{{ $style['h5'] }}">Mini ATM/EDC BRI</h5>
                                                <ol style="{{ $style['ol'] }}">
                                                    <li>Pilih menu Mini ATM &gt; Pembayaran &gt; BRIVA</li>
                                                    <li> <span class="italic">Swipe</span> Kartu Debit BRI Anda </li>
                                                    <li>Masukkan Nomor <i>Virtual Account</i> anda <span class="amount">{{ $cust_code }}</span></li>
                                                    <li>Masukkan PIN</li>
                                                    <li>Di halaman konfirmasi, pastikan detil pembayaran sudah sesuai seperti Nomor BRIVA, Nama Pelanggan dan Jumlah Pembayaran</li>
                                                    <li>Simpan struk transaksi sebagai bukti pembayaran</li>
                                                </ol>


                                                <h5 style="{{ $style['h5'] }}">Kantor Bank BRI</h5>
                                                <ol style="{{ $style['ol'] }}">
                                                    <li>Ambil nomor antrian transaksi Teller dan isi slip setoran</li>
                                                    <li>Serahkan slip dan jumlah setoran kepada Teller BRI</li>
                                                    <li>Teller BRI akan melakukan validasi transaksi</li>
                                                    <li>Simpan slip setoran hasil validasi sebagai bukti pembayaran</li>
                                                </ol>

                                                <h5 style="{{ $style['h5'] }}">ATM Bank Lain</h5>
                                                <ol style="{{ $style['ol'] }}">
                                                    <li>Masukkan Kartu Debit dan PIN Anda</li>
                                                    <li>Pilih menu Transaksi Lainnya &gt; Transfer &gt; Ke Rek Bank Lain</li>
                                                    <li>Masukkan kode bank BRI (002) kemudian diikuti dengan Nomor <i>Virtual Account</i> anda <span class="amount">{{ $cust_code }}</span> </li>
                                                    <li>Ikuti instruksi untuk menyelesaikan transaksi</li>
                                                    <li>Simpan struk transaksi sebagai bukti pembayaran</li>
                                                </ol>
                                            <!-- end cara bayar -->
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td>
                                <table style="{{ $style['email-footer'] }}" align="center" width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="{{ $fontFamily }} {{ $style['email-footer_cell'] }}">
                                            <p style="{{ $style['paragraph-sub'] }}">
                                                &copy; {{ date('Y') }}
                                                SIAKAD STIE Nobel Indonesia
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
