<?php

// Admin
Route::group(['middleware' => ['auth:web','role:admin|akademik|cs|jurusan|personalia|keuangan|karyawan|ketua 1']], function(){

	Route::get('/', 'BerandaController@index')->name('beranda_admin');

	Route::group(['prefix' => 'pengumuman'], function(){
		Route::get('/', 'PengumumanController@index')->name('pengumuman');
		Route::get('/create', 'PengumumanController@create')->name('pengumuman_create');
		Route::post('/store', 'PengumumanController@store')->name('pengumuman_store');
		Route::get('/edit/{id}', 'PengumumanController@edit')->name('pengumuman_edit');
		Route::post('/update/{id?}', 'PengumumanController@update')->name('pengumuman_update');
		Route::get('/delete/{id?}', 'PengumumanController@delete')->name('pengumuman_delete');
	});

	Route::group(['prefix' => 'kalender'], function(){
		Route::get('/', 'KalenderAkademikController@index')->name('kalender');
		Route::get('/create', 'KalenderAkademikController@create')->name('kalender_create');
		Route::post('/store', 'KalenderAkademikController@store')->name('kalender_store');
		Route::get('/edit/{id}', 'KalenderAkademikController@edit')->name('kalender_edit');
		Route::post('/update/{id?}', 'KalenderAkademikController@update')->name('kalender_update');
		Route::get('/delete/{id?}', 'KalenderAkademikController@delete')->name('kalender_delete');
		Route::get('/order', 'KalenderAkademikController@order')->name('kalender_order');
	});

	Route::group(['prefix' => 'literatur'], function(){
		Route::get('/', 'LiteraturController@index')->name('literatur');
		Route::get('/create', 'LiteraturController@create')->name('literatur_create');
		Route::post('/store', 'LiteraturController@store')->name('literatur_store');
		Route::get('/edit/{id}', 'LiteraturController@edit')->name('literatur_edit');
		Route::post('/update', 'LiteraturController@update')->name('literatur_update');
		Route::get('/delete/{id?}', 'LiteraturController@delete')->name('literatur_delete');
	});

	Route::group(['prefix' => 'jurnal'], function(){
		Route::get('/', 'JurnalController@index')->name('jurnal');
		Route::get('/create', 'JurnalController@create')->name('jurnal_create');
		Route::post('/store', 'JurnalController@store')->name('jurnal_store');
		Route::get('/edit/{id}', 'JurnalController@edit')->name('jurnal_edit');
		Route::post('/update', 'JurnalController@update')->name('jurnal_update');
		Route::get('/delete/{id?}', 'JurnalController@delete')->name('jurnal_delete');
	});

	Route::group(['prefix' => 'gbpp'], function(){
		Route::get('/', 'GbppController@index')->name('gbpp');
		Route::get('/create', 'GbppController@create')->name('gbpp_create');
		Route::post('/store', 'GbppController@store')->name('gbpp_store');
		Route::get('/edit/{id}', 'GbppController@edit')->name('gbpp_edit');
		Route::post('/update', 'GbppController@update')->name('gbpp_update');
		Route::get('/delete/{id?}', 'GbppController@delete')->name('gbpp_delete');
	});

	Route::group(['prefix' => 'pedoman'], function(){
		Route::get('/', 'PedomanAkademikController@index')->name('pedoman');
		Route::get('/create', 'PedomanAkademikController@create')->name('pedoman_create');
		Route::post('/store', 'PedomanAkademikController@store')->name('pedoman_store');
		Route::get('/edit/{id}', 'PedomanAkademikController@edit')->name('pedoman_edit');
		Route::post('/update/{id?}', 'PedomanAkademikController@update')->name('pedoman_update');
		Route::get('/delete/{id?}', 'PedomanAkademikController@delete')->name('pedoman_delete');
		Route::get('/order', 'PedomanAkademikController@order')->name('pedoman_order');
	});

	Route::group(['prefix' => 'album'], function(){
		Route::get('/', 'AlbumController@index')->name('album');
		Route::post('/store', 'AlbumController@store')->name('album_store');
		Route::get('/delete/{id?}', 'AlbumController@delete')->name('album_delete');
		Route::get('/urutan', 'AlbumController@urutan')->name('album_urutan');
	});

	Route::group(['prefix' => 'galeri'], function(){
		Route::get('/', 'GaleriController@index')->name('galeri');
		Route::get('/create', 'GaleriController@create')->name('galeri_create');
		Route::post('/store', 'GaleriController@store')->name('galeri_store');
		Route::get('/edit/{id}', 'GaleriController@edit')->name('galeri_edit');
		Route::post('/update/{id?}', 'GaleriController@update')->name('galeri_update');
		Route::get('/delete/{id?}', 'GaleriController@delete')->name('galeri_delete');
	});

	Route::group(['prefix' => 'about'], function(){
		Route::get('/profil', 'AboutController@profil')->name('profil');
		Route::post('/update-profil', 'AboutController@updateProfil')->name('profil_update');
		Route::get('/visi', 'AboutController@visi')->name('visi');
		Route::post('/update-visi', 'AboutController@updateVisi')->name('visi_update');
		Route::get('/keunggulan', 'AboutController@keunggulan')->name('keunggulan');
		Route::post('/update-keunggulan', 'AboutController@updateKeunggulan')->name('keunggulan_update');
		Route::get('/prodi', 'AboutController@prodi')->name('prodi');
		Route::post('/update-prodi', 'AboutController@updateProdi')->name('prodi_update');
		Route::get('/fasilitas', 'AboutController@fasilitas')->name('fasilitas');
		Route::get('/fasilitas-create', 'AboutController@fasilitasCreate')->name('fasilitas_create');
		Route::post('/fasilitas-store', 'AboutController@fasilitasStore')->name('fasilitas_store');
		Route::get('/fasilitas-edit', 'AboutController@fasilitasEdit')->name('fasilitas_edit');
		Route::post('/fasilitas-update/{id?}', 'AboutController@fasilitasUpdate')->name('fasilitas_update');
		Route::get('/fasilitas-delete/{id?}', 'AboutController@fasilitasDelete')->name('fasilitas_delete');
		Route::get('/fasilitas-urutan', 'AboutController@fasilitasUrutan')->name('fasilitas_urutan');
		Route::get('/peta', 'AboutController@peta')->name('peta');
		Route::get('/peta-create', 'AboutController@petaCreate')->name('peta_create');
		Route::post('/peta-store', 'AboutController@petaStore')->name('peta_store');
		Route::get('/peta-edit', 'AboutController@petaEdit')->name('peta_edit');
		Route::post('/peta-update/{id?}', 'AboutController@petaUpdate')->name('peta_update');
		Route::get('/peta-delete/{id?}', 'AboutController@petaDelete')->name('peta_delete');
		Route::get('/peta-urutan', 'AboutController@petaUrutan')->name('peta_urutan');
	});

	Route::group(['prefix' => 'slide'], function(){
		Route::get('/', 'SlideController@index')->name('slide');
		Route::get('/create', 'SlideController@create')->name('slide_create');
		Route::post('/store', 'SlideController@store')->name('slide_store');
		Route::get('/edit/{id}', 'SlideController@edit')->name('slide_edit');
		Route::post('/update/{id?}', 'SlideController@update')->name('slide_update');
		Route::get('/delete/{id?}', 'SlideController@delete')->name('slide_delete');
		Route::get('/order', 'SlideController@order')->name('slide_order');
	});

	Route::group(['prefix' => 'saran'], function(){
		Route::get('/', 'SaranController@index')->name('saran');
		Route::get('/delete/{id?}', 'SaranController@delete')->name('saran_delete');
	});

	Route::group(['prefix' => 'akun'], function(){
		Route::get('/', 'AkunController@index')->name('akun');
		Route::post('/update', 'AkunController@update')->name('akun_update');
		Route::get('/update-password', 'AkunController@UpdatePassword')->name('akun_update_password');
	});

});