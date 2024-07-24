<?php

use Illuminate\Http\Request;
use App\Http\Controllers\api\tracerstudy\TracerStudyController;


Route::group(['namespace' => 'api', 'middleware' => 'cors'], function(){
    Route::post('import-mahasiswa','MahasiswaController@import');
    Route::get('login', 'LoginController@login');
    Route::get('mahasiswa-lulus','tracerstudy\TracerStudyController@mahasiswaLulus');
    Route::post('v1/application','ApplicationController@store');
});


Route::group(['namespace' => 'api\informasi', 'middleware' => 'cors'], function(){

	Route::group(['prefix' => 'pengumuman'], function(){
		Route::get('/', 'PengumumanController@index');
		Route::get('/detail/{id}', 'PengumumanController@detail');
	});

	Route::group(['prefix' => 'kalender'], function(){
		Route::get('/', 'KalenderAkademikController@index');
	});

	Route::group(['prefix' => 'literatur'], function(){
		Route::get('/', 'LiteraturController@index');
	});

	Route::group(['prefix' => 'jurnal'], function(){
		Route::get('/', 'JurnalController@index');
	});

	Route::group(['prefix' => 'gbpp'], function(){
		Route::get('/', 'GbppController@index');
	});

	Route::group(['prefix' => 'slide'], function(){
		Route::get('/', 'SlideController@index');
	});

	Route::group(['prefix' => 'pedoman'], function(){
		Route::get('/', 'PedomanAkademikController@index');
		Route::get('/detail/{id}', 'PedomanAkademikController@detail');
	});

	Route::group(['prefix' => 'about'], function(){
		Route::get('/profil', 'AboutController@profil');
		Route::get('/visi', 'AboutController@visi');
		Route::get('/keunggulan', 'AboutController@keunggulan');
		Route::get('/prodi', 'AboutController@prodi');
		Route::get('/fasilitas', 'AboutController@fasilitas');
		Route::get('/peta', 'AboutController@peta');
	});

	Route::group(['prefix' => 'album'], function(){
		Route::get('/', 'GaleriController@index');
		Route::get('/galeri', 'GaleriController@galeri');
	});

	Route::group(['prefix' => 'saran'], function(){
		Route::get('/store', 'SaranController@store');
	});

	Route::get('/upload', 'UploadController@upload');
});

Route::group(['namespace' => 'api', 'middleware' => 'cors'], function(){
	Route::get('/mail-pembayaran-reminder/{nim}', 'KeuanganController@mailPembayaranReminder');
	Route::get('/mail-pembayaran-batal/{nim}', 'KeuanganController@mailPembayaranBatal');
	Route::get('/mail-pembayaran-sukses/{nim}/{id}', 'KeuanganController@mailPembayaranSukses');
	Route::get('/versi', 'VersiController@index');

	Route::get('/mhs/pembayaran', 'KeuanganController@index');
	Route::get('/mhs/pembayaran/cek/{id_briva?}', 'KeuanganController@cekPembayaran');
	Route::get('/mhs/pembayaran/store', 'KeuanganController@store');
	Route::get('/mhs/pembayaran/delete', 'KeuanganController@delete');
	Route::get('/mhs/pembayaran/delete-briva/{code}', 'KeuanganController@deleteAkunBriva');
	Route::get('/pembayaran/report/{tgl}', 'KeuanganController@report');
	Route::get('/mhs/pembayaran/history', 'KeuanganController@history');
	Route::get('/mhs/pembayaran/jenis', 'KeuanganController@jenis');

	Route::get('/mhs/info-kuliah/cari', 'MahasiswaController@cari');
	Route::get('/mhs/info-kuliah', 'MahasiswaController@jdkMahasiswa');
	Route::get('/mhs/info-kuliah/detail', 'MahasiswaController@mhsAbsen');

	Route::get('/mhs/pilih-konsentrasi', 'PilihKonsentrasiController@index');
	Route::post('/mhs/pilih-konsentrasi/store', 'PilihKonsentrasiController@store');

	Route::get('/jadwal', 'JadwalController@index');

	Route::get('/kartu-ujian', 'KartuUjianController@index');

	Route::get('/status-kartu-ujian', 'KartuUjianController@statusKartu');
	Route::get('/status-kartu-ujian/detail', 'KartuUjianController@statusKartuDetail');

	Route::get('/nilai', 'NilaiController@index');
	Route::get('/transkrip', 'NilaiController@transkrip');

	Route::get('/dosen', 'DosenController@index');
	Route::get('/dosen/{id}', 'DosenController@profil');
	Route::get('/penilaian', 'DosenController@penilaian');
	Route::get('/penilaian/detail', 'DosenController@penilaianDetail');
	Route::get('/penilaian/hitung', 'DosenController@penilaianHitung');
	Route::post('/penilaian/store', 'DosenController@penilaianStore');

	Route::get('/absensi', 'DosenController@absensi');
	Route::get('/absensi/detail', 'DosenController@absensiDetail');
	Route::post('/absensi/buka-absensi', 'DosenController@bukaAbsensi');
	Route::post('/absensi/store', 'DosenController@absensiStore');
	Route::post('/absensi/dosen/store', 'DosenController@absensiDosenStore');

	Route::get('/kuesioner', 'kuesionerController@index');
	Route::get('/kuesioner/add', 'kuesionerController@add');
	Route::post('/kuesioner/store', 'kuesionerController@store');

	Route::get('/krs', 'KrsController@index');
	Route::get('/store-tmp', 'KrsController@storeTmp');
	Route::post('/store', 'KrsController@store');

	Route::get('/lms/mhs', 'LmsMhsController@index');
	Route::get('/lms/mhs/detail', 'LmsMhsController@detail');
	Route::post('/lms/mhs/materi-view', 'LmsMhsController@materiView');
	Route::get('/lms/mhs/tugas-detail', 'LmsMhsController@tugasDetail');
	// Route::get('/lms/mhs/kuis-detail', 'LmsMhsController@kuisDetail');

	Route::get('/lms/materi/view/{id_materi}/{id_dosen}/{file}', 'LmsMhsController@materiView')->name('view_materi');

	Route::get('/nilai-seminar', 'NilaiUjianSeminarController@index');
	Route::get('/nilai-seminar/detail', 'NilaiUjianSeminarController@detail');
	Route::get('/nilai-seminar/edit', 'NilaiUjianSeminarController@edit');
	Route::post('/nilai-seminar', 'NilaiUjianSeminarController@update');

	Route::get('/persetujuan-seminar', 'PersetujuanSeminarController@index');
	Route::post('/persetujuan-seminar', 'PersetujuanSeminarController@update');

	Route::get('/bimbingan', 'BimbinganController@index');

	Route::get('/mhs/bimbingan', 'BimbinganMahasiswaController@index');
	Route::get('/mhs/bimbingan/riwayat', 'BimbinganMahasiswaController@riwayat');
	Route::get('/mhs/bimbingan/download', 'BimbinganMahasiswaController@download');
	Route::get('/mhs/bimbingan/lampiran', 'BimbinganMahasiswaController@lampiran');

	Route::get('/mhs/absensi', 'AbsensiMhsController@index');
	Route::get('/mhs/absensi/detail/{id_jdk}', 'AbsensiMhsController@detail');
	Route::post('/mhs/absensi/store', 'AbsensiMhsController@store');

});
