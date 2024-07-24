<?php
// use DB;

// Route::get('/tempe', function(){
// 	return DB::table('nilai')->where('id_mhs_reg','123')->get();

// });

Route::get('/', function () {
	if ( Auth::check() ) {
    	return redirect(url('beranda'));
	} else {
    	return redirect(url('login'));
    }
});
Route::get('/sevima/update-nilai','SevimaImportController@updateIndexNilai');
Route::get('/sevima/test','SevimaImportController@test');
Route::get('/sevima/seri-ijazah','SevimaImportController@seriIjazah');
Route::get('/sevima/delete-mahasiswa','SevimaImportController@deleteMahasiswa');
Route::get('/sevima/mahasiswa','SevimaImportController@mahasiswa');
Route::get('/sevima/users','SevimaImportController@users');
Route::get('/sevima/yudisium','SevimaImportController@yudisium');
Route::get('/sevima/lulusan','SevimaImportController@lulusan');
Route::get('/sevima/kelas','SevimaImportController@kelas');
Route::get('/sevima/nilai-pindahan','SevimaImportController@nilaiPindahan');
Route::get('/sevima/mata-kuliah','SevimaImportController@mataKuliah');
Route::get('/sevima/nilai','SevimaImportController@nilai');
Route::get('/sevima/krs','SevimaImportController@krs');
Route::get('/sevima/dosen-mengajar','SevimaImportController@dosenMengajar');

//Route::get('/inputpt','TestingController@inputPT');
Route::get('/test', 'TestingController@index');
Route::get('/xml', 'TestingController@parseXml');

Route::get('/import-wilayah', 'MahasiswaController@importWilayah');

Route::get('/home', function(){
	return redirect('/beranda');
});

Route::group(['middleware' => 'minify'], function(){
	Auth::routes();
});

Route::get('/relogin/{id}', 'BerandaController@reLogin')->name('relogin');

// Pengelola
	Route::group(['middleware' => 'auth:web'], function() {

		Route::get('/beranda', 'BerandaController@index');

		Route::get('/set-filter', 'BerandaController@setFilter')->name('session_filter');
		
		Route::get('/get-perguruan-tinggi', 'BerandaController@getPerguruanTinggi')->name('get_pt');
		Route::get('/get-all-prodi', 'BerandaController@getAllProdi')->name('get_all_prodi');

		Route::get('/detail-akm', 'BerandaController@detailAkm')->name('detail_akm');
		Route::get('/detail-akm-total', 'BerandaController@detailAkmTotal')->name('detail_akm_total');
		Route::get('/detail-akm-lulus', 'BerandaController@detailAkmLulus')->name('detail_akm_lulus');
		// Route::get('/detail-ipk-aktif', 'BerandaController@detailIpkAktif')->name('detail_ipk_aktif');
		Route::get('/detail-ipk-lulus', 'BerandaController@detailIpkLulus')->name('detail_ipk_lulus');

		Route::get('/keadaan-mhs', 'BerandaController@keadaanMhs')->name('keadaan_mhs');
		Route::get('/keadaan-dosen', 'BerandaController@keadaanDosen')->name('keadaan_dosen');

		Route::get('/kalender-akademik', 'BerandaController@kalenderAkademik')->name('mhs_kalender');
		
		Route::get('/aplikasi/down', 'BerandaController@shutdown');
		Route::get('/aplikasi/up', 'BerandaController@start');

		Route::get('/validasi', 'BerandaController@validasi')->name('validasi');
		
		Route::group(['middleware' => ['role:admin|akademik']], function(){
			Route::get('/uuid', 'BerandaController@uuid');
		});
		
		Route::get('/ubah-periode', 'BerandaController@ubahPeriode')->name('ubah_periode');

		Route::get('/id-mhs-reg', 'BerandaController@idMhsReg');
		
		Route::get('/ubah-nim', 'BerandaController@ubahNim')->name('ubah_nim');
		Route::get('/cek-mhs-forlap', 'BerandaController@cekMhsForlap')->name('cek_mhs_forlap');

	});

	Route::group(['prefix' => 'maba', 'middleware' => 'role:admin|akademik|cs|personalia'], function(){
		Route::get('/', 'MahasiswaBaruController@index')->name('maba');
		Route::post('/cari', 'MahasiswaBaruController@cari')->name('maba_cari');
		Route::get('/filter', 'MahasiswaBaruController@setFilter')->name('maba_filter');
		Route::get('/add', 'MahasiswaBaruController@add')->name('maba_add');
		Route::post('/impor-massal', 'MahasiswaBaruController@imporMassal')->name('maba_impor_massal');
		Route::post('/store', 'MahasiswaBaruController@store')->name('maba_store');
		Route::get('/impor', 'MahasiswaBaruController@impor')->name('maba_impor');
		Route::get('/edit/{id?}', 'MahasiswaBaruController@edit')->name('maba_edit');
		Route::post('/update', 'MahasiswaBaruController@update')->name('maba_update');
		Route::post('/cari', 'MahasiswaBaruController@cari')->name('maba_cari');
		Route::get('/delete/{id?}', 'MahasiswaBaruController@delete')->name('maba_delete');
	});

	Route::group(['prefix' => 'mahasiswa', 'middleware' => 'auth:web'], function(){
		
		Route::group(['middleware' => ['role:keuangan']], function(){
			Route::get('/update-bebas-pembayaran', 'MahasiswaController@updateBebasPembayaran')->name('mahasiswa_bebas_bayar');
		});

		Route::group(['middleware' => ['role:pustakawan']], function(){
			Route::get('/update-bebas-pustaka', 'MahasiswaController@updateBebasPustaka')->name('mahasiswa_bebas_pustaka');
		});

		Route::group(['middleware' => ['role:jurusan']], function(){
			Route::get('/update-bebas-skripsi', 'MahasiswaController@updateBebasSkripsi')->name('mahasiswa_bebas_skripsi');
		});

		Route::group(['middleware' => ['role:admin|akademik']], function(){
			// Route::get('/feeder/store', 'MahasiswaController@feederStore')->name('m_feeder_store');
			// Route::get('/feeder/store-reg-pd/{id}', 'MahasiswaController@feederStoreRegpd')->name('m_feeder_store_reg_pd');
			// Route::get('/feeder/update-reg-pd/{id}', 'MahasiswaController@feederUpdateRegpd');
			// Route::get('/feeder/store-konfersi/{id}', 'MahasiswaController@feederStoreKonfersi');
			// Route::get('/feeder/delete-konfersi/{id}', 'MahasiswaController@feederDeleteKonfersi');
		});

		// Route::group(['middleware' => ['role:admin|akademik|personalia']], function(){
		Route::group(['middleware' => ['role:admin|akademik|personalia|cs']], function(){
			Route::get('/relogin/{id_user}', 'MahasiswaController@reLogin')->name('mahasiswa_relogin');
			Route::get('/add', 'MahasiswaController@add')->name('mahasiswa_add');
			Route::get('/get-pmb', 'MahasiswaController@getPmb')->name('mahasiswa_pmb');
			Route::post('/impor', 'MahasiswaController@impor')->name('mahasiswa_impor');
			Route::post('/store', 'MahasiswaController@store')->name('mahasiswa_store');
			Route::get('/edit/{id}', 'MahasiswaController@edit')->name('mahasiswa_edit');
			Route::post('/update', 'MahasiswaController@update')->name('mahasiswa_update');
			Route::post('/update-akun', 'MahasiswaController@updateAkun')->name('mahasiswa_update_akun');
			Route::get('/delete/{id?}', 'MahasiswaController@delete')->name('mahasiswa_delete');
			Route::post('/updatefoto', 'MahasiswaController@updatefoto')->name('mahasiswa_updatefoto');
			Route::post('/regpd/store', 'MahasiswaController@storeRegpd')->name('mahasiswa_regpdstore');
			Route::post('/regpd/update', 'MahasiswaController@updateRegpd')->name('mahasiswa_regpdupdate');
			Route::get('/regpd/delete/{id?}', 'MahasiswaController@deleteRegpd')->name('mahasiswa_regpddelete');
			Route::post('/krs/store', 'MahasiswaController@storeKrs')->name('mahasiswa_krs_store');
			Route::get('/krs/delete/{id?}', 'MahasiswaController@deleteKrs')->name('mahasiswa_krs_delete');
			Route::post('/pasang-pin', 'MahasiswaController@pasangPin')->name('mahasiswa_pasang_pin');
			Route::post('/store-dokumen', 'MahasiswaController@dokumenStore')->name('mahasiswa_doc_store');
			Route::get('/delete-dokumen/{id?}', 'MahasiswaController@dokumenDelete')->name('mahasiswa_doc_delete');
			Route::get('/ubah-judul-dokumen', 'MahasiswaController@dokumenEdit')->name('mahasiswa_doc_edit');
			Route::get('/download-dokumen', 'MahasiswaController@dokumenDownload')->name('mahasiswa_doc_download');
		});

		Route::group(['middleware' => ['role:admin|jurnal']], function(){
			Route::get('/publish', 'MahasiswaController@jurnalPublish')->name('mahasiswa_jurnal_publish');
			Route::get('/store-revisi', 'MahasiswaController@storeRevisiJurnal')->name('mahasiswa_jurnal_revisi');
			Route::get('/mail-revisi', 'MahasiswaController@sendMailRevisi')->name('mahasiswa_jurnal_mail_revisi');
		});
		
		Route::group(['middleware' => ['role:admin|jurnal|akademik|cs']], function(){
			Route::get('/jurnal-delete', 'MahasiswaController@jurnalFileDelete')->name('mahasiswa_jurnal_file_delete');
			Route::get('/jurnal/{id}', 'MahasiswaController@jurnal')->name('mahasiswa_jurnal');
			Route::post('/jurnal-store', 'MahasiswaController@jurnalStore')->name('mahasiswa_jurnal_store');
			Route::get('/jurnal-download/{file?}', 'MahasiswaController@jurnalDownload')->name('mahasiswa_jurnal_download');
		});
		Route::post('/kartu-mahasiswa/crop', 'MahasiswaController@kartuMhsCrop')->name('mahasiswa_kartu_mhs_crop');
		Route::get('/kartu-mahasiswa-preview', 'MahasiswaController@kartuMhsPrev')->name('mahasiswa_kartu_mhs_prev');
		Route::get('/kartu-mahasiswa-cetak', 'MahasiswaController@kartuMhsCetak')->name('mahasiswa_kartu_mhs_cetak');
		Route::get('/kartu-mahasiswa-cetak-depan', 'MahasiswaController@kartuMhsCetakSisiDepan')->name('mahasiswa_kartu_mhs_sisi_depan');

		Route::get('/', 'MahasiswaController@index')->name('mahasiswa');
		Route::get('/print', 'MahasiswaController@eksporPrint')->name('mahasiswa_print');
		Route::get('/cetak-sk-kuliah/{id?}', 'MahasiswaController@skKuliah')->name('mahasiswa_sk_kuliah');
		Route::get('/excel', 'MahasiswaController@eksporExcel')->name('mahasiswa_excel');
		Route::get('/konsentrasi', 'MahasiswaController@konsentrasi')->name('mahasiswa_konsentrasi');
		Route::get('/filter', 'MahasiswaController@filter')->name('mahasiswa_filter');
		Route::post('/cari', 'MahasiswaController@cari')->name('mahasiswa_cari');
		Route::get('/negara', 'MahasiswaController@negara')->name('mahasiswa_negara');
		Route::get('/kecamatan', 'MahasiswaController@kecamatan')->name('mahasiswa_kecamatan');
		Route::get('/detail/{id?}', 'MahasiswaController@detail')->name('mahasiswa_detail');
		Route::get('/regpd/{id?}', 'MahasiswaController@regPd')->name('mahasiswa_regpd');
		Route::get('/regpd/edit/{id?}', 'MahasiswaController@editRegpd')->name('mahasiswa_editregpd');
		Route::get('/krs/{id?}', 'MahasiswaController@krs')->name('mahasiswa_krs');
		Route::get('/get-jadwal', 'MahasiswaController@getJadwal')->name('mahasiswa_load_jadwal');
		Route::get('/aktivitas/{id?}', 'MahasiswaController@aktivitas')->name('mahasiswa_aktivitas');
		Route::get('/get-krs', 'MahasiswaController@getKrs')->name('mahasiswa_get_krs');
		Route::get('/get-kurikulum', 'MahasiswaController@getKurikulum')->name('mahasiswa_get_kurikulum');
		Route::get('/cari-nim', 'MahasiswaController@cariNim')->name('mahasiswa_cari_nim');
		Route::get('/nilai/{id?}', 'MahasiswaController@nilai')->name('mahasiswa_nilai');
		Route::get('/cetak-nilai', 'MahasiswaController@nilaiCetak')->name('mahasiswa_nilai_cetak');
        Route::get('/simpan-nilai/{id}', 'MahasiswaController@simpanNilai')->name('mahasiswa_simpan_nilai');
		Route::post('/update-nilai', 'MahasiswaController@nilaiUpdate')->name('mahasiswa_nilai_update');
		
		Route::get('/konfersi/{id?}', 'MahasiswaController@nilaiKonfersi')->name('mahasiswa_konfersi');
		Route::get('/cetak-konfersi', 'MahasiswaController@cetakKonfersi')->name('mahasiswa_konfersi_cetak');
		Route::get('/get-mk', 'MahasiswaController@getMk')->name('mahasiswa_niltransfer_get_mk');
		Route::post('/store-konfersi', 'MahasiswaController@storeKonfersi')->name('mahasiswa_konfersi_store');
		Route::get('/delete-konfersi/{id?}', 'MahasiswaController@deleteNilaiTransfer')->name('mahasiswa_konfersi_delete');
		
		Route::get('/jadwal-kuliah/{id?}', 'MahasiswaController@jadwalKuliah')->name('mahasiswa_jdk');

		Route::get('/transkrip/{id?}', 'MahasiswaController@transkrip')->name('mahasiswa_transkrip');
		Route::get('/transkrip/cetak/{id?}', 'MahasiswaController@transkripCetak')->name('mahasiswa_transkrip_cetak');
		Route::get('/transkrip-sementara/cetak/{id?}', 'MahasiswaController@transkripSementaraCetak')->name('mahasiswa_transkrip_sementara_cetak');
		Route::get('/ijazah/cetak/{id?}', 'MahasiswaController@ijazahCetak')->name('mahasiswa_ijazah_cetak');
	});
	
	Route::group(['prefix' => 'absensi', 'middleware' => ['role:admin|akademik|cs|jurusan|ketua 1', 'auth:web']], function(){
		Route::get('/absen', 'AbsensiController@index')->name('absen');
		Route::get('/absen/mhs', 'AbsensiController@absen')->name('absen_mhs');
		Route::get('/absen/mhs-cetak', 'AbsensiController@cetakPerJadwal')->name('absen_mhs_cetak');
		Route::get('/absen/mhs/detail', 'AbsensiController@absenMhs')->name('absen_mhs_detail');
		Route::get('/absen/mhs/detail-cetak', 'AbsensiController@cetakPerMk')->name('absen_mhs_detail_cetak');
	});

	Route::group(['prefix' => 'dosen', 'middleware' => 'auth:web'], function(){

		Route::group(['middleware' => 'role:admin|akademik|cs|personalia'], function(){
			Route::get('/add', 'DosenController@add')->name('dosen_add');
			Route::post('/store', 'DosenController@store')->name('dosen_store');
			Route::post('/impor', 'DosenController@impor')->name('dosen_impor');
			Route::get('/edit/{id?}', 'DosenController@edit')->name('dosen_edit');
			Route::post('/update', 'DosenController@update')->name('dosen_update');
			Route::get('/delete/{id?}', 'DosenController@delete')->name('dosen_delete');
			Route::get('/login/{id_user}', 'DosenController@login')->name('dosen_login');
		});

		Route::group(['prefix' => 'kegiatan', 'middleware' => 'role:admin|akademik|personalia|ketua 1|jurusan'], function(){
			Route::get('/', 'DosenController@kegiatan')->name('dosen_kegiatan');
			Route::get('/get-data', 'DosenController@kegiatanData')->name('dosen_kegiatan_data');
			Route::get('/detail/{id_dosen}', 'DosenController@kegiatanDetail')->name('dosen_kegiatan_detail');
			Route::get('/filter', 'DosenController@kegiatanFilter')->name('dosen_kegiatan_filter');
			Route::get('/view/{id}/{id_dosen}/{file}', 'DosenController@kegiatanViewDok')->name('dosen_kegiatan_viewdok');
			Route::get('/download/{id_dosen}', 'DosenController@kegiatanDownload')->name('dosen_kegiatan_download');
			Route::get('/delete/{id}', 'DosenController@kegiatanDelete')->name('dosen_kegiatan_delete');
		});

		Route::get('/', 'DosenController@index')->name('dosen');
		Route::post('/cari', 'DosenController@cari')->name('dosen_cari');
		Route::get('/filter', 'DosenController@filter')->name('dosen_filter');
		Route::get('/ekspor/excel', 'DosenController@eksporExcel')->name('dosen_excel');
		Route::get('/ekspor/print', 'DosenController@eksporPrint')->name('dosen_print');

		Route::get('/mengajar', 'DosenController@dosenMengajar')->name('dosen_mengajar');
		Route::get('/mengajar/cetak', 'DosenController@dosenMengajarCetak')->name('dosen_mengajar_cetak');
		Route::get('/mengajar/sk/{id?}', 'DosenController@dosenMengajarSk')->name('dosen_mengajar_sk');
		Route::get('/mengajar/get-mk', 'DosenController@dosenMengajarSkMk')->name('dosen_mengajar_sk_mk');
		Route::post('/mengajar/sk2', 'DosenController@dosenMengajarSk2')->name('dosen_mengajar_sk2');
		Route::get('/absensi-dosen', 'DosenController@absensiDosen')->name('dosen_absensi');
	});

	Route::group(['prefix' => 'pilih-konsentrasi', 'middleware' => 'auth:web'], function(){

		Route::group(['middleware' => 'role:admin|akademik|cs'], function(){
			Route::get('/add', 'PilihKonsentrasiController@add')->name('konsentrasi_add');
			Route::post('/store', 'PilihKonsentrasiController@store')->name('konsentrasi_store');
			Route::get('/edit/{id?}', 'PilihKonsentrasiController@edit')->name('konsentrasi_edit');
			Route::post('/update', 'PilihKonsentrasiController@update')->name('konsentrasi_update');
			Route::get('/delete/{id?}', 'PilihKonsentrasiController@delete')->name('konsentrasi_delete');
		});

		Route::get('/', 'PilihKonsentrasiController@index')->name('konsentrasi');
		Route::get('/get-mhs', 'PilihKonsentrasiController@getMahasiswa')->name('konsentrasi_get_mhs');
		Route::post('/cari', 'PilihKonsentrasiController@cari')->name('konsentrasi_cari');
		Route::get('/set-filter', 'PilihKonsentrasiController@setFilter')->name('konsentrasi_filter');
		Route::get('/ekspor/excel', 'PilihKonsentrasiController@eksporExcel')->name('konsentrasi_excel');
		Route::get('/ekspor/print', 'PilihKonsentrasiController@eksporPrint')->name('konsentrasi_print');

	});

	Route::group(['prefix' => 'daftar-sp', 'middleware' => 'role:admin|akademik|jurusan'], function(){
		Route::post('/store', 'DaftarSpController@store')->name('daftar_sp_store');
		Route::post('/update', 'DaftarSpController@update')->name('daftar_sp_update');
		Route::get('/delete/{id?}', 'DaftarSpController@delete')->name('daftar_sp_delete');

		Route::get('/', 'DaftarSpController@index')->name('daftar_sp');
		Route::get('/get-mhs', 'DaftarSpController@getMhs')->name('daftar_sp_mhs');
		Route::post('/cari', 'DaftarSpController@cari')->name('daftar_sp_cari');
		Route::get('/filter', 'DaftarSpController@filter')->name('daftar_sp_filter');
	});

	Route::group(['prefix' => 'krs-mhs', 'middleware' => 'role:admin|akademik|personalia|cs|ketua 1'], function(){
		Route::get('/', 'MahasiswaKrsController@index')->name('mhs_krs_lap');
		Route::get('/cari', 'MahasiswaKrsController@cari')->name('mhs_krs_lap_cari');
		Route::get('/filter', 'MahasiswaKrsController@filter')->name('mhs_krs_lap_filter');
		Route::get('/rollback', 'MahasiswaKrsController@rollback')->name('mhs_krs_lap_rollback');
		Route::get('/cetak/{id?}', 'MahasiswaKrsController@cetak')->name('mhs_krs_lap_cetak');
		Route::get('/excel', 'MahasiswaKrsController@excel')->name('mhs_krs_lap_excel');
	});

	Route::group(['prefix' => 'matakuliah', 'middleware' => 'auth:web'], function(){

		Route::group(['middleware' => 'role:admin|akademik'], function(){
			Route::get('/add', 'MatakuliahController@add')->name('matakuliah_add');
			Route::get('/matakuliah', 'MatakuliahController@matakuliah')->name('matakuliah_list');
			Route::post('/store', 'MatakuliahController@store')->name('matakuliah_store');
			Route::get('/edit/{id?}', 'MatakuliahController@edit')->name('matakuliah_edit');
			Route::post('/update', 'MatakuliahController@update')->name('matakuliah_update');
			Route::get('/delete/{id?}', 'MatakuliahController@delete')->name('matakuliah_delete');
		});

		Route::get('/', 'MatakuliahController@index')->name('matakuliah');
		Route::get('/detail/{id?}', 'MatakuliahController@detail')->name('matakuliah_detail');
		Route::post('/cari', 'MatakuliahController@cari')->name('matakuliah_cari');
		Route::get('/filter', 'MatakuliahController@filter')->name('matakuliah_filter');
		Route::get('/ekspor/excel', 'MatakuliahController@eksporExcel')->name('matakuliah_excel');
		Route::get('/ekspor/print', 'MatakuliahController@eksporPrint')->name('matakuliah_print');

	});

	Route::group(['prefix' => 'kurikulum', 'middleware' => 'auth:web'], function(){

		Route::group(['middleware' => 'role:admin|akademik'], function(){
			Route::get('/add', 'KurikulumController@add')->name('kurikulum_add');
			Route::post('/store', 'KurikulumController@store')->name('kurikulum_store');
			Route::get('/edit/{id?}', 'KurikulumController@edit')->name('kurikulum_edit');
			Route::post('/update', 'KurikulumController@update')->name('kurikulum_update');
			Route::get('/delete/{id?}', 'KurikulumController@delete')->name('kurikulum_delete');

			Route::post('/matakuliah/store', 'KurikulumController@mkStore')->name('kurikulum_mk_store');
			Route::get('/matakuliah/delete/{id?}', 'KurikulumController@mkDelete')->name('kurikulum_mk_delete');
			Route::get('/matakuliah/add/{id?}', 'KurikulumController@mkAdd')->name('kurikulum_mk_add');
			Route::post('/matakuliah/store-arr', 'KurikulumController@mkStoreArr')->name('kurikulum_mk_store_arr');
			Route::get('/matakuliah/salin', 'KurikulumController@mkSalin')->name('kurikulum_mk_salin');
		});

		Route::get('/', 'KurikulumController@index')->name('kurikulum');
		Route::get('/matakuliah', 'KurikulumController@matakuliah')->name('kurikulum_matakuliah');
		Route::get('/detail/{id?}', 'KurikulumController@detail')->name('kurikulum_detail');
		Route::post('/cari', 'KurikulumController@cari')->name('kurikulum_cari');
		Route::get('/filter', 'KurikulumController@filter')->name('kurikulum_filter');
		Route::get('/ekspor/excel', 'KurikulumController@eksporExcel')->name('kurikulum_excel');
		Route::get('/ekspor/print', 'KurikulumController@eksporPrint')->name('kurikulum_print');

	});

	Route::group(['prefix' => 'jadwal-kuliah', 'middleware' => 'auth:web'], function(){

		Route::group(['middleware' => 'role:admin|akademik'], function(){
			Route::get('/add', 'JadwalKuliahController@add')->name('jdk_add');
			Route::get('/add-dosen', 'JadwalKuliahController@addDosen')->name('jdk_add_dosen');
			Route::post('/store', 'JadwalKuliahController@store')->name('jdk_store');
			Route::post('/store-s2', 'JadwalKuliahController@storeS2')->name('jdk_store_s2');
			Route::get('/edit/{id?}', 'JadwalKuliahController@edit')->name('jdk_edit');
			Route::post('/update', 'JadwalKuliahController@update')->name('jdk_update');
			Route::post('/update-s2', 'JadwalKuliahController@updateS2')->name('jdk_update_s2');
			Route::get('/delete/{id?}', 'JadwalKuliahController@delete')->name('jdk_delete');
			Route::post('/store-pertemuan', 'JadwalKuliahController@storePertemuan')->name('jdk_store_pertemuan');
			Route::post('/update-pertemuan', 'JadwalKuliahController@updatePertemuan')->name('jdk_update_pertemuan');
			Route::get('/delete-pertemuan/{id?}', 'JadwalKuliahController@deletePertemuan')->name('jdk_delete_pertemuan');
			Route::post('/dosen/store', 'JadwalKuliahController@dosenStore')->name('jdk_dosen_store');
			Route::post('/dosen/update', 'JadwalKuliahController@dosenUpdate')->name('jdk_dosen_update');
			Route::get('/dosen/delete', 'JadwalKuliahController@dosenDelete')->name('jdk_dosen_delete');
			Route::get('/mhs', 'JadwalKuliahController@mahasiswa')->name('jdk_mhs');
			Route::post('/mhs/store', 'JadwalKuliahController@mahasiswaStore')->name('jdk_mhs_store');
			Route::get('/mhs/delete/{id}', 'JadwalKuliahController@mahasiswaDelete')->name('jdk_mhs_delete');
			Route::get('/mhs/add', 'JadwalKuliahController@mahasiswaAdd')->name('jdk_mhs_add');
			Route::post('/mhs/store-arr', 'JadwalKuliahController@mahasiswaStoreArr')->name('jdk_mhs_store_arr');
			Route::get('/mhs/add-from-krs', 'JadwalKuliahController@mahasiswaAddFromKrs')->name('jdk_mhs_add_krs');
			Route::post('/mhs/store-from-krs', 'JadwalKuliahController@mahasiswaStoreFromKrs')->name('jdk_mhs_store_krs');
		});

		Route::group(['middleware' => ['role:admin|akademik']], function(){
			Route::get('/feeder/store-jdk/{id_jdk}', 'JadwalKuliahController@feederStore');
			// Route::get('/feeder/update-jdk/{id_jdk}', 'JadwalKuliahController@feederUpdate');
		});

		Route::get('/', 'JadwalKuliahController@index')->name('jdk');
		Route::get('/detail/{id?}', 'JadwalKuliahController@detail')->name('jdk_detail');
		Route::get('/detail/cetak/{id?}', 'JadwalKuliahController@cetakPeserta')->name('jdk_cetak_peserta');
		Route::post('/cari', 'JadwalKuliahController@cari')->name('jdk_cari');
		Route::get('/ajax', 'JadwalKuliahController@ajax')->name('jdk_ajax');
		Route::get('/matakuliah', 'JadwalKuliahController@matakuliah')->name('jdk_matakuliah');
		Route::get('/dosen', 'JadwalKuliahController@dosen')->name('jdk_dosen');
		Route::get('/filter', 'JadwalKuliahController@filter')->name('jdk_filter');
		Route::get('/ekspor/print', 'JadwalKuliahController@eksporPrint')->name('jdk_print');
		Route::get('/cetak-absen-mhs', 'JadwalKuliahController@cetakAbsenMhs')->name('jdk_cetak_absen_mhs');
		Route::get('/cetak-absen-dosen', 'JadwalKuliahController@cetakAbsenDosen')->name('jdk_cetak_absen_dosen');
		Route::get('/cetak-label-absen', 'JadwalKuliahController@cetakLabelAbsen')->name('jdk_cetak_label_absen');

	});

	Route::group(['prefix' => 'jadwal-antara', 'middleware' => 'auth:web'], function(){

		Route::group(['middleware' => 'role:admin|akademik'], function(){
			Route::get('/add', 'JadwalAntaraController@add')->name('jda_add');
			Route::get('/add-dosen', 'JadwalAntaraController@addDosen')->name('jda_add_dosen');
			Route::post('/store', 'JadwalAntaraController@store')->name('jda_store');
			Route::get('/edit/{id?}', 'JadwalAntaraController@edit')->name('jda_edit');
			Route::post('/update', 'JadwalAntaraController@update')->name('jda_update');
			Route::get('/delete/{id?}', 'JadwalAntaraController@delete')->name('jda_delete');
			Route::post('/dosen/store', 'JadwalAntaraController@dosenStore')->name('jda_dosen_store');
			Route::post('/dosen/update', 'JadwalAntaraController@dosenUpdate')->name('jda_dosen_update');
			Route::get('/dosen/delete', 'JadwalAntaraController@dosenDelete')->name('jda_dosen_delete');
			Route::get('/mhs', 'JadwalAntaraController@mahasiswa')->name('jda_mhs');
			Route::post('/mhs/store', 'JadwalAntaraController@mahasiswaStore')->name('jda_mhs_store');
			Route::get('/mhs/delete/{id}', 'JadwalAntaraController@mahasiswaDelete')->name('jda_mhs_delete');
			Route::get('/mhs/add', 'JadwalAntaraController@mahasiswaAdd')->name('jda_mhs_add');
			Route::post('/mhs/store-arr', 'JadwalAntaraController@mahasiswaStoreArr')->name('jda_mhs_store_arr');
		});

		Route::get('/', 'JadwalAntaraController@index')->name('jda');
		Route::get('/detail/{id?}', 'JadwalAntaraController@detail')->name('jda_detail');
		Route::post('/cari', 'JadwalAntaraController@cari')->name('jda_cari');
		Route::get('/ajax', 'JadwalAntaraController@ajax')->name('jda_ajax');
		Route::get('/matakuliah', 'JadwalAntaraController@matakuliah')->name('jda_matakuliah');
		Route::get('/dosen', 'JadwalAntaraController@dosen')->name('jda_dosen');
		Route::get('/filter', 'JadwalAntaraController@filter')->name('jda_filter');
		Route::get('/ekspor/print', 'JadwalAntaraController@eksporPrint')->name('jda_print');
		Route::get('/cetak-absen-mhs', 'JadwalAntaraController@cetakAbsenMhs')->name('jda_cetak_absen_mhs');
		Route::get('/cetak-absen-dosen', 'JadwalAntaraController@cetakAbsenDosen')->name('jda_cetak_absen_dosen');
		Route::get('/cetak-daftar-nilai', 'JadwalAntaraController@cetakDaftarNilai')->name('jda_cetak_daftar_nilai');

	});

	Route::group(['prefix' => 'jadwal-ujian', 'middleware' => 'auth:web'], function(){

		Route::group(['middleware' => 'role:admin|akademik'], function(){
			Route::get('/add', 'JadwalUjianController@add')->name('jdu_add');
			Route::post('/store', 'JadwalUjianController@store')->name('jdu_store');
			Route::get('/edit/{id?}', 'JadwalUjianController@edit')->name('jdu_edit');
			Route::post('/update', 'JadwalUjianController@update')->name('jdu_update');
			Route::get('/delete/{id?}', 'JadwalUjianController@delete')->name('jdu_delete');
		});

		Route::get('/', 'JadwalUjianController@index')->name('jdu');
		Route::get('/detail/{id?}', 'JadwalUjianController@detail')->name('jdu_detail');
		Route::get('/pengawas', 'JadwalUjianController@pengawas')->name('jdu_pengawas');
		Route::get('/filter', 'JadwalUjianController@filter')->name('jdu_filter');
		Route::get('/ekspor/print', 'JadwalUjianController@eksporPrint')->name('jdu_print');
		Route::get('/get-mhs', 'JadwalUjianController@getMhs')->name('jdu_get_mhs');
		Route::get('/print-kartu-ujian', 'JadwalUjianController@printKartuUjian')->name('jdu_print_ku');
		Route::get('/print-absensi-ujian/{id?}', 'JadwalUjianController@printAbsensiUjian')->name('jdu_print_absensi_ujian');
		Route::get('/print-label-ujian', 'JadwalUjianController@printLabelUjian')->name('jdu_print_label_ujian');

	});

	Route::group(['prefix' => 'status-krs', 'middleware' => 'auth:web'], function(){

		Route::group(['middleware' => 'role:admin|akademik'], function(){
			Route::get('/update', 'StatusKrsController@update')->name('status_krs_update');
		});

		Route::get('/', 'StatusKrsController@index')->name('status_krs');
		Route::post('/cari', 'StatusKrsController@cari')->name('status_krs_cari');
		Route::get('/filter', 'StatusKrsController@setFilter')->name('status_krs_filter');
	});

	Route::group(['prefix' => 'pengawas', 'middleware' => 'auth:web'], function(){

		Route::group(['middleware' => 'role:admin|akademik'], function(){
			Route::post('/pengawas/store', 'PengawasController@store')->name('pengawas_store');
			Route::post('/pengawas/update', 'PengawasController@update')->name('pengawas_update');
			Route::get('/pengawas/delete/{id}', 'PengawasController@delete')->name('pengawas_delete');
		});

		Route::get('/', 'PengawasController@index')->name('pengawas');
			
	});

	Route::group(['prefix' => 'nilai', 'middleware' => 'auth:web'], function(){

		Route::group(['middleware' => 'role:admin|akademik|ketua 1'], function(){
			Route::get('/delete/{id?}', 'NilaiController@delete')->name('nil_delete');
		});
		
		Route::get('/edit/{id?}', 'NilaiController@edit')->name('nil_edit');
		Route::get('/detail/{id?}', 'NilaiController@detail')->name('nil_detail');
		Route::post('/update', 'NilaiController@update')->name('nil_update');
		Route::post('/update-s2', 'NilaiController@updateS2')->name('nil_update_s2');
		Route::get('/hitung-nilai-s2', 'NilaiController@hitungNilaiS2')->name('nil_hitung_s2');

		Route::get('/', 'NilaiController@index')->name('nil');
		Route::get('/cetak/{id?}', 'NilaiController@cetak')->name('nil_cetak');

	});

	Route::group(['prefix' => 'ujian-akhir', 'middleware' => 'auth:web'], function(){

		Route::group(['middleware' => 'role:admin|akademik'], function(){
			Route::post('/nilai/store', 'UjianAkhirController@nilaiStore')->name('ua_nilai_store');
		});

		Route::post('/store', 'UjianAkhirController@pengujiStore')->name('ua_penguji_store');
		Route::get('/', 'UjianAkhirController@index')->name('ua');
		Route::get('/penguji', 'UjianAkhirController@penguji')->name('ua_penguji');
		Route::get('/nilai', 'UjianAkhirController@nilai')->name('ua_nilai');

		Route::get('/grade', 'UjianAkhirController@grade')->name('grade');

		Route::get('/berita-acara', 'UjianAkhirController@beritaAcara')->name('ua_berita_acara');
		Route::get('/rekap-nilai', 'UjianAkhirController@rekapNilai')->name('ua_rekap_nilai');
		Route::get('/cetak-rekap-nilai', 'UjianAkhirController@cetakRekapNilai')->name('ua_cetak_rekap_nilai');
		Route::get('/cetak-surat-pernyataan-tesis', 'UjianAkhirController@cetakPernyataan')->name('ua_cetak_pernyataan');
		Route::get('/cetak-jadwal-seminar', 'UjianAkhirController@cetakJadwalSeminar')->name('ua_cetak_jadwal_seminar');
		Route::get('/ekspor-telah-ujian', 'UjianAkhirController@eksporTelahUjian')->name('ua_ekspor_telah_ujian');
	});

	Route::group(['prefix' => 'aktivitas', 'middleware' => 'auth:web'], function(){

		Route::group(['middleware' => 'role:admin|akademik'], function(){
			Route::get('/add', 'AktivitasController@add')->name('akm_add');
			Route::post('/store', 'AktivitasController@store')->name('akm_store');
			Route::get('/edit', 'AktivitasController@edit')->name('akm_edit');
			Route::post('/update', 'AktivitasController@update')->name('akm_update');
			Route::post('/store-arr/{id?}', 'AktivitasController@storeArr')->name('akm_store_arr');
			Route::get('/delete/{id?}', 'AktivitasController@delete')->name('akm_delete');
		});

		Route::get('/', 'AktivitasController@index')->name('akm');
		Route::get('/excel-feeder', 'AktivitasController@excelFeeder')->name('akm_excel_feeder');
		Route::get('/hitung-akm', 'AktivitasController@hitungAkm')->name('akm_hitung');
		Route::get('/hitung-akm-sp', 'AktivitasController@hitungAkmSp')->name('akm_hitung_sp');
		Route::post('/cari', 'AktivitasController@cari')->name('akm_cari');
		Route::get('/filter', 'AktivitasController@filter')->name('akm_filter');
		Route::get('/mhs', 'AktivitasController@mhs')->name('akm_mhs');
		Route::get('/cetak', 'AktivitasController@cetak')->name('akm_cetak');

	});

	Route::group(['prefix' => 'lulus-keluar', 'middleware' => 'auth:web'], function(){

		Route::group(['middleware' => 'role:admin|akademik'], function(){
			Route::get('/add', 'LulusKeluarController@add')->name('lk_add');
			Route::post('/store', 'LulusKeluarController@store')->name('lk_store');
			Route::get('/edit/{id}', 'LulusKeluarController@edit')->name('lk_edit');
			Route::post('/update', 'LulusKeluarController@update')->name('lk_update');
			Route::get('/delete/{id?}', 'LulusKeluarController@delete')->name('lk_delete');
		});

		Route::get('/', 'LulusKeluarController@index')->name('lk');
		Route::post('/cari', 'LulusKeluarController@cari')->name('lk_cari');
		Route::get('/filter', 'LulusKeluarController@filter')->name('lk_filter');
		Route::get('/mhs', 'LulusKeluarController@mhs')->name('lk_mhs');
		Route::get('/cetak', 'LulusKeluarController@cetak')->name('lk_print');
		Route::get('/ekspor', 'LulusKeluarController@ekspor')->name('lk_ekspor');
		Route::get('/html', 'LulusKeluarController@html')->name('lk_html');
		Route::get('/cetak-sk-lulus/{id}', 'LulusKeluarController@cetakSkLulus')->name('lk_sk_lulus');
		Route::get('/detail/{id?}', 'LulusKeluarController@detail')->name('lk_detail');
		
		Route::get('/berita-acara-yudisium', 'LulusKeluarController@beritaAcaraYudisium')->name('lk_berita_acara_yudisium');
		Route::get('/berita-acara-yudisium/cetak', 'LulusKeluarController@beritaAcaraYudisiumCetak')->name('lk_berita_acara_yudisium_cetak');

	});

	Route::group(['prefix' => 'master', 'middleware' => 'auth:web'], function(){

		Route::group(['middleware' => 'role:admin|akademik|ketua 1'], function(){
			Route::post('/prodi/store', 'MasterController@storeProdi')->name('m_prodi_store');
			Route::get('/prodi/edit/{id?}', 'MasterController@editProdi')->name('m_prodi_edit');
			Route::post('/prodi/update', 'MasterController@updateProdi')->name('m_prodi_update');
			Route::get('/prodi/delete/{id?}', 'MasterController@deleteProdi')->name('m_prodi_delete');

			Route::post('/fakultas/store', 'MasterController@storeFakultas')->name('m_fakultas_store');
			Route::post('/fakultas/update', 'MasterController@updateFakultas')->name('m_fakultas_update');
			Route::get('/fakultas/delete/{id?}', 'MasterController@deleteFakultas')->name('m_fakultas_delete');

			Route::post('/konsentrasi/store', 'MasterController@storeKonsentrasi')->name('m_konsentrasi_store');
			Route::get('/konsentrasi/edit/{id?}', 'MasterController@editKonsentrasi')->name('m_konsentrasi_edit');
			Route::post('/konsentrasi/update', 'MasterController@updateKonsentrasi')->name('m_konsentrasi_update');
			Route::get('/konsentrasi/delete/{id?}', 'MasterController@deleteKonsentrasi')->name('m_konsentrasi_delete');

			Route::post('/skala-nilai/store', 'MasterController@storeSkalaNilai')->name('m_skalanilai_store');
			Route::get('/skala-nilai/edit/{id?}', 'MasterController@editSkalaNilai')->name('m_skalanilai_edit');
			Route::post('/skala-nilai/update', 'MasterController@updateSkalaNilai')->name('m_skalanilai_update');
			Route::get('/skala-nilai/delete/{id?}', 'MasterController@deleteSkalaNilai')->name('m_skalanilai_delete');

			Route::post('/kelas/store', 'MasterController@storeKelas')->name('m_kelas_store');
			Route::post('/kelas/update', 'MasterController@updateKelas')->name('m_kelas_update');
			Route::get('/kelas/delete/{id?}', 'MasterController@deleteKelas')->name('m_kelas_delete');

			Route::post('/jam-kuliah/store', 'MasterController@storeJamkuliah')->name('m_jamkuliah_store');
			Route::post('/jam-kuliah/update', 'MasterController@updateJamkuliah')->name('m_jamkuliah_update');
			Route::get('/jam-kuliah/delete/{id?}', 'MasterController@deleteJamkuliah')->name('m_jamkuliah_delete');

			Route::post('/ruangan/store', 'MasterController@storeRuangan')->name('m_ruangan_store');
			Route::post('/ruangan/update', 'MasterController@updateRuangan')->name('m_ruangan_update');
			Route::get('/ruangan/delete/{id?}', 'MasterController@deleteRuangan')->name('m_ruangan_delete');			
		});

		Route::get('/prodi', 'MasterController@prodi')->name('m_prodi');
		Route::get('/fakultas', 'MasterController@fakultas')->name('m_fakultas');
		Route::get('/konsentrasi', 'MasterController@konsentrasi')->name('m_konsentrasi');
		Route::get('/skala-nilai', 'MasterController@skalaNilai')->name('m_skalanilai');
		Route::get('/kelas', 'MasterController@kelas')->name('m_kelas');
		Route::get('/ruangan', 'MasterController@ruangan')->name('m_ruangan');
		Route::get('/jam-kuliah', 'MasterController@jamkuliah')->name('m_jamkuliah');

		Route::group(['middleware' => 'role:admin'], function(){
			Route::get('/semester', 'MasterController@semester')->name('m_semester');
			Route::post('/semester/store', 'MasterController@storeSemester')->name('m_semester_store');
			Route::post('/semester/update', 'MasterController@updateSemester')->name('m_semester_update');
			Route::get('/semester/update-status', 'MasterController@updateStatusSemester')->name('m_semester_update_status');
			Route::get('/semester/delete/{id?}', 'MasterController@deleteSemester')->name('m_semester_delete');

			Route::get('/mahasiswa-kip', 'MahasiswaController@mhsKip')->name('mhsKip');
			Route::get('/cetak/mahasiswa-kip', 'MahasiswaController@cetakMhsKip')->name('ctkMhsKip');
		});

	});

	Route::group(['prefix' => 'lms', 'middleware' => ['auth:web', 'role:admin|akademik|ketua 1|jurusan']], function(){
		Route::get('/', 'LmsController@index')->name('lms');
		Route::get('/detail/{id_dosen}/{id_jdk}', 'LmsController@detail')->name('lms_detail');
		Route::get('/materi/view/{id_materi?}/{id_dosen?}/{file?}', 'LmsController@materiView')->name('lms_materi_view');
		Route::get('/topik/get', 'LmsController@topik')->name('lms_topik');
		Route::post('/cari', 'LmsController@cari')->name('lms_cari');
		Route::get('/set-filter', 'LmsController@setFilter')->name('lms_filter');
		Route::get('/get-penggunaan', 'LmsController@penggunaan')->name('lms_penggunaan');
		Route::get('/ekspor-penggunaan', 'LmsController@penggunaanEkspor')->name('lms_penggunaan_ekspor');
	});

	Route::group(['prefix' => 'materi-pasca', 'middleware' => ['auth:web', 'role:admin|akademik']], function(){
		Route::get('/', 'MateriPascaController@index')->name('materi');
		Route::get('/detail', 'MateriPascaController@detail')->name('materi_detail');
		Route::post('/cari', 'MateriPascaController@cari')->name('materi_cari');
		Route::get('/add', 'MateriPascaController@add')->name('materi_add');
		Route::get('/matakuliah', 'MateriPascaController@matakuliah')->name('materi_matakuliah');
		Route::post('/store', 'MateriPascaController@store')->name('materi_store');
		Route::get('/edit', 'MateriPascaController@edit')->name('materi_edit');
		Route::post('/update', 'MateriPascaController@update')->name('materi_update');
		Route::get('/delete', 'MateriPascaController@delete')->name('materi_delete');
		Route::get('/materi/view/{id?}/{file?}', 'MateriPascaController@materiPascaDownload')->name('materi_download');
	});

	Route::group(['prefix' => 'keuangan', 'middleware' => ['auth:web','role:keuangan|admin']], function(){

		Route::get('/history-briva', 'KeuanganController@historyBriva')->name('keu_history_briva');

		Route::get('/tunggakan', 'TunggakanController@index')->name('keu_tunggakan');
		Route::get('/tunggakan/cetak', 'TunggakanController@cetak')->name('keu_tunggakan_cetak');
		
		Route::get('/add', 'KeuanganController@add')->name('keu_add');
		Route::post('/store', 'KeuanganController@store')->name('keu_store');
		Route::post('/impor', 'KeuanganController@impor')->name('keu_impor');
		Route::post('/praktek/store', 'KeuanganController@praktekStore')->name('keu_praktek_store');
		Route::post('/sp/store', 'KeuanganController@spStore')->name('keu_sp_store');
		Route::get('/edit', 'KeuanganController@edit')->name('keu_edit');
		Route::post('/update', 'KeuanganController@update')->name('keu_update');
		Route::get('/delete/{id?}', 'KeuanganController@delete')->name('keu_delete');

		Route::get('/', 'KeuanganController@index')->name('keu');
		Route::get('/detail/{id}', 'KeuanganController@detail')->name('keu_detail');
		Route::get('/data-pembayaran', 'KeuanganController@dataPembayaran')->name('keu_data_pembayaran');
		Route::get('/data-pembayaran-praktek', 'KeuanganController@dataPembayaranPraktek')->name('keu_data_pembayaran_praktek');
		Route::get('/data-pembayaran-sp', 'KeuanganController@dataPembayaranSp')->name('keu_data_pembayaran_sp');
		Route::post('/cari', 'KeuanganController@cari')->name('keu_cari');
		Route::get('/cetak-langsung', 'KeuanganController@cetakLangsung')->name('keu_cetak_langsung');
		Route::get('/cetak', 'KeuanganController@cetak')->name('keu_cetak');
		Route::get('/ekspor', 'KeuanganController@ekspor')->name('keu_ekspor');
		Route::get('/cetak-detail/{id}', 'KeuanganController@cetakDetail')->name('keu_cetak_detail');

		Route::get('/detail/praktek/{id}', 'KeuanganController@praktekDetail')->name('keu_detail_praktek');
		Route::get('/cetak-langsung-praktek', 'KeuanganController@praktekCetakLangsung')->name('keu_cetak_langsung_praktek');
		Route::get('/cetak-praktek', 'KeuanganController@praktekCetak')->name('keu_cetak_praktek');
		Route::get('/ekspor-praktek', 'KeuanganController@praktekEkspor')->name('keu_ekspor_praktek');
		Route::get('/cetak-praktek-detail/{id}', 'KeuanganController@praktekCetakDetail')->name('keu_cetak_praktek_detail');

		Route::get('/praktek', 'KeuanganController@praktek')->name('keu_praktek');
		Route::post('/delete', 'KeuanganController@praktekDelete')->name('keu_praktek_delete');
		Route::get('/mahasiswa', 'KeuanganController@mahasiswa')->name('keu_mhs');

		Route::get('/sp/detail/{id}', 'KeuanganController@spDetail')->name('keu_detail_sp');
		Route::get('/sp/cetak-langsung', 'KeuanganController@spCetakLangsung')->name('keu_cetak_langsung_sp');
		Route::get('/sp/cetak', 'KeuanganController@spCetak')->name('keu_cetak_sp');
		Route::get('/sp/ekspor', 'KeuanganController@spEkspor')->name('keu_ekspor_sp');
		Route::get('/sp/cetak-detail/{id}', 'KeuanganController@spCetakDetail')->name('keu_cetak_sp_detail');

		Route::get('/set-all-sudah-bayar', 'KeuanganController@setAllSudahBayar')->name('keu_set_all_sb');

		Route::get('/sp', 'KeuanganController@sp')->name('keu_sp');

		Route::get('/kartu/kartu-ujian', 'KartuUjianController@index')->name('ku');
		Route::get('/kartu-ujian/update', 'KartuUjianController@update')->name('ku_update');
		

	});

	Route::group(['prefix' => 'keuangan/konfirmasi-bayar', 'middleware' => ['auth:web','role:keuangan|admin']], function(){

		Route::get('/', 'KonfirmasiBayarController@index')->name('keu_konfir');
		Route::get('/filter', 'KonfirmasiBayarController@setFilter')->name('keu_konfir_filter');
		Route::get('/cari', 'KonfirmasiBayarController@cari')->name('keu_konfir_cari');
		Route::post('/store', 'KonfirmasiBayarController@store')->name('keu_konfir_store');
		Route::post('/delete', 'KonfirmasiBayarController@delete')->name('keu_konfir_delete');
		Route::get('/view-file/{file}', 'KonfirmasiBayarController@view')->name('keu_konfir_view');

	});


	Route::group(['prefix' => 'briva', 'middleware' => ['auth:web','role:admin']], function(){
		Route::get('/akun', 'BrivaController@akun')->name('briva_akun');
		Route::get('/akun/delete/{id}', 'BrivaController@akunDelete')->name('briva_delete');
	});

	Route::group(['prefix' => 'status-kartu-ujian', 'middleware' => 'auth:web'], function(){

		Route::group(['middleware' => 'role:karyawan|admin|akademik|pengawas'], function(){
			Route::get('/', 'KartuUjianController@statusKartu')->name('ku_status');
			Route::get('/detail/{id?}', 'KartuUjianController@statusKartuDetail')->name('ku_status_detail');
		});
	});

	Route::group(['prefix' => 'master-keuangan', 'middleware' => 'auth:web'], function(){

		Route::group(['middleware' => 'role:keuangan|admin'], function(){
			Route::post('/bank/store', 'MasterKeuanganController@storeBank')->name('ms_bank_store');
			Route::post('/bank/update', 'MasterKeuanganController@updateBank')->name('ms_bank_update');
			Route::get('/bank/delete/{id?}', 'MasterKeuanganController@deleteBank')->name('ms_bank_delete');

			Route::get('/biaya/edit', 'MasterKeuanganController@editBiaya')->name('ms_biaya_edit');
			Route::post('/biaya/update', 'MasterKeuanganController@updateBiaya')->name('ms_biaya_update');
			Route::get('/biaya/delete/{id?}', 'MasterKeuanganController@deleteBiaya')->name('ms_biaya_delete');

			Route::get('/bank', 'MasterKeuanganController@bank')->name('ms_bank');
			Route::get('/biaya', 'MasterKeuanganController@biaya')->name('ms_biaya');

			Route::get('/potongan', 'PotonganBiayaController@index')->name('pot');
			Route::post('/impor-potongan', 'PotonganBiayaController@impor')->name('pot_impor');
			Route::get('/potongan/cetak', 'PotonganBiayaController@cetak')->name('pot_cetak');
			Route::get('/potongan/edit', 'PotonganBiayaController@edit')->name('pot_edit');
			Route::get('/potongan/mhs', 'PotonganBiayaController@mhs')->name('pot_mhs');
			Route::post('/potongan/update', 'PotonganBiayaController@update')->name('pot_update');
			Route::post('/potongan/store', 'PotonganBiayaController@store')->name('pot_store');
			Route::get('/potongan/delete/{id?}', 'PotonganBiayaController@delete')->name('pot_delete');

		});
	});

	Route::group(['prefix' => 'setting', 'middleware' => 'auth:web'], function(){

		Route::group(['middleware' => 'role:admin|akademik'], function(){
			Route::get('/', 'SettingController@index')->name('set');
			Route::post('/update', 'SettingController@update')->name('set_update');
		});
	});

	Route::group(['prefix' => 'jadwal-akademik', 'middleware' => 'auth:web'], function(){

		Route::group(['middleware' => 'role:admin|akademik'], function(){
			Route::get('/', 'JadwalAkademikController@index')->name('ja');
			Route::get('/edit/{id?}', 'JadwalAkademikController@edit')->name('ja_edit');
			Route::post('/update', 'JadwalAkademikController@update')->name('ja_update');
		});
	});

	Route::group(['prefix' => 'users', 'middleware' => 'auth:web'], function(){
		Route::group(['middleware' => 'role:admin'], function(){
			Route::get('/', 'UsersController@index')->name('users');
			Route::get('/add', 'UsersController@add')->name('users_add');
			Route::post('/store', 'UsersController@store')->name('users_store');
			Route::get('/edit', 'UsersController@edit')->name('users_edit');
			Route::post('/update', 'UsersController@update')->name('users_update');
			Route::get('/delete/{id}', 'UsersController@delete')->name('users_delete');
			Route::get('/relogin/{id}', 'UsersController@reLogin')->name('users_relogin');
		});
		
		Route::get('/ubah-profil', 'UsersController@profil')->name('users_profil');
		Route::post('/update-profil', 'UsersController@updateProfil')->name('users_update_profil');
	});

	Route::group(['prefix' => 'naik-semester', 'middleware' => 'auth:web'], function(){

		Route::group(['middleware' => 'role:admin|akademik'], function(){
			Route::get('/', 'NaikSemesterController@index')->name('naik_smt');
			Route::post('/store', 'NaikSemesterController@store')->name('naik_smt_store');
		});
	});


	Route::group(['prefix' => 'kuesioner', 'middleware' => 'auth:web'], function(){

		Route::group(['prefix' => 'master', 'middleware' => 'role:admin|akademik|jurusan'], function(){
			
			Route::get('/ajax', 'KuesionerMasterController@ajax')->name('kues_ajax');
			
			Route::get('/komponen', 'KuesionerMasterController@komponen')->name('kues_komponen');
			Route::post('/komponen/store', 'KuesionerMasterController@storeKomponen')->name('kues_komponen_store');
			Route::post('/komponen/update', 'KuesionerMasterController@updateKomponen')->name('kues_komponen_update');
			Route::get('/komponen/delete/{id?}', 'KuesionerMasterController@deleteKomponen')->name('kues_komponen_delete');
		
			Route::get('/komponen/isi', 'KuesionerMasterController@komponenIsi')->name('kues_komponen_isi');
			Route::post('/komponen/isi/store', 'KuesionerMasterController@storeKomponenIsi')->name('kues_komponen_isi_store');
			Route::post('/komponen/isi/update', 'KuesionerMasterController@updateKomponenIsi')->name('kues_komponen_isi_update');
			Route::get('/komponen/isi/delete/{id?}', 'KuesionerMasterController@deleteKomponenIsi')->name('kues_komponen_isi_delete');
			
			Route::get('/jadwal', 'KuesionerMasterController@jadwal')->name('kues_jadwal');
			Route::post('/jadwal/store', 'KuesionerMasterController@storeJadwal')->name('kues_jadwal_store');
			Route::post('/jadwal/update', 'KuesionerMasterController@updateJadwal')->name('kues_jadwal_update');
			Route::get('/jadwal/delete/{id?}', 'KuesionerMasterController@deleteJadwal')->name('kues_jadwal_delete');
		});

		Route::group(['prefix' => 'hasil', 'middleware' => 'role:admin|akademik|jurusan'], function(){
			Route::get('/', 'KuesionerController@index')->name('kues_hasil');
			Route::get('/detail', 'KuesionerController@detail')->name('kues_hasil_detail');
			Route::get('/cetak', 'KuesionerController@cetak')->name('kues_hasil_cetak');
			Route::get('/cetak-detail', 'KuesionerController@cetakPerMk')->name('kues_hasil_cetak_detail');
		});
	});

	Route::group(['prefix' => 'pkm', 'middleware' => 'role:admin|jurusan|akademik'], function(){
		Route::get('/', 'PkmController@index')->name('pkm');
		Route::post('/cari', 'PkmController@cari')->name('pkm_cari');
		Route::get('/set-filter', 'PkmController@setFilter')->name('pkm_filter');
		Route::get('/get-mahasiswa', 'PkmController@getMahasiswa')->name('pkm_get_mahasiswa2');
		Route::get('/get-dosen', 'PkmController@getDosen')->name('pkm_get_dosen2');
		Route::get('/daftar', 'PkmController@daftar')->name('pkm_daftar');
		Route::get('/edit', 'PkmController@edit')->name('pkm_edit');
		Route::post('/update', 'PkmController@update')->name('pkm_update');
		Route::post('/store', 'PkmController@store')->name('pkm_store');
		Route::post('/anggota-store', 'PkmController@anggotaStore')->name('pkm_anggota_store');
		Route::get('/anggota-delete', 'PkmController@anggotaDelete')->name('pkm_anggota_delete');
		Route::post('/dosen-store', 'PkmController@dosenStore')->name('pkm_dosen_store');
		Route::get('/dosen-delete', 'PkmController@dosenDelete')->name('pkm_dosen_delete');
		Route::get('/detail/{id?}', 'PkmController@detail')->name('pkm_detail');
		Route::get('/delete/{id?}', 'PkmController@delete')->name('pkm_delete');
	});

	Route::group(['prefix' => 'kelas-mahasiswa', 'middleware' => 'role:admin|akademik'], function(){
		Route::get('/', 'KelasController@index')->name('kelas');
		Route::post('/cari', 'KelasController@cari')->name('kelas_cari');
		Route::get('/set-filter', 'KelasController@setFilter')->name('kelas_set_filter');
		Route::get('/ekspor', 'KelasController@ekspor')->name('kelas_ekspor');
		Route::get('/print', 'KelasController@prin')->name('kelas_print');
		Route::get('/filter-non-kelas', 'KelasController@nonKelasFilter')->name('kelas_non_kelas');
		Route::post('/update', 'KelasController@update')->name('kelas_update');
	});


	Route::group(['prefix' => 'pendaftar-seminar', 'middleware' => 'role:keuangan|admin'], function(){
		Route::get('/', 'PendaftarSeminarController@index')->name('seminar');
		Route::get('/set-filter', 'PendaftarSeminarController@setFilter')->name('seminar_filter');
		Route::post('/update', 'PendaftarSeminarController@update')->name('seminar_update');
	});

	Route::group(['prefix' => 'validasi-ndc', 'middleware' => 'role:ndc|admin'], function(){
		Route::get('/', 'ValidasiNdcController@index')->name('val_ndc');
		Route::get('/set-filter', 'ValidasiNdcController@setFilter')->name('val_ndc_filter');
		Route::get('/detail', 'ValidasiNdcController@detail')->name('val_ndc_detail');
		Route::get('/proses', 'ValidasiNdcController@proses')->name('val_ndc_proses');
	});

	Route::group(['prefix' => 'validasi-ijazah', 'middleware' => 'role:jurusan|pustakawan|keuangan'], function(){
		Route::get('/', 'ValidasiPengambilanIjazahController@index')->name('val_ijazah');
		Route::get('/download', 'ValidasiPengambilanIjazahController@download')->name('val_ijazah_download');
		Route::get('/update', 'ValidasiPengambilanIjazahController@update')->name('val_ijazah_update');
		Route::get('/filter', 'ValidasiPengambilanIjazahController@setFilter')->name('val_ijazah_filter');
	});

	Route::group(['prefix' => 'mbkm', 'middleware' => 'role:admin|akademik|jurusan'], function(){
		Route::get('/', 'MbkmController@index')->name('mbkm');
		Route::get('/filter', 'MbkmController@filter')->name('mbkm_filter');
		Route::post('/cari', 'MbkmController@cari')->name('mbkm_cari');
		Route::get('/add', 'MbkmController@add')->name('mbkm_add');
		Route::post('/store', 'MbkmController@store')->name('mbkm_store');
		Route::get('/edit/{id}', 'MbkmController@edit')->name('mbkm_edit');
		Route::post('/update', 'MbkmController@update')->name('mbkm_update');
		Route::get('/delete/{id}', 'MbkmController@delete')->name('mbkm_delete');

		Route::get('/detail/{id?}', 'MbkmController@detail')->name('mbkm_detail');
		Route::get('/mhs', 'MbkmController@mhs')->name('mbkm_mhs');
		Route::post('/store-peserta', 'MbkmController@storePeserta')->name('mbkm_store_peserta');
		Route::get('/delete-peserta/{id?}', 'MbkmController@deletePeserta')->name('mbkm_delete_peserta');

		Route::get('/dosen', 'MbkmController@dosen')->name('mbkm_dosen');
		Route::post('/store-dosen', 'MbkmController@storeDosen')->name('mbkm_store_dosen');
		Route::get('/delete-dosen/{id?}', 'MbkmController@deleteDosen')->name('mbkm_delete_dosen');

		Route::get('/konversi', 'MbkmController@conversiMbkm')->name('konversi_mbkm');
		Route::get('/konversi/{id}', 'MbkmController@detailMbkm')->name('detail_konversi');
		Route::get('/konversi/show-nilai/{id}', 'MbkmController@showNilai')->name('show_nilai_mbkm');                                                                                    
		Route::post('/konversi/insert/mbkm', 'MbkmController@storeNilai')->name('insertNilaiMBKM');
		Route::get('/konversi/del-nilai/{id}', 'MbkmController@destroyNilai')->name('deleteNilMbkm');
	});

// semester
	Route::group(['prefix' => 'semester', 'middleware' => 'auth:web'], function() {

		Route::get('/', 'SemesterController@index');

	});
// end semester

/* Mahasiswa Member */
	Route::group(['prefix' => 'mhs/konfirmasi-bayar', 'namespace' => 'mahasiswa', 'middleware' => ['auth:web','minify']], function(){

		Route::get('/', 'KonfirmasiBayarController@index')->name('mhs_konfir');
		Route::post('/store', 'KonfirmasiBayarController@store')->name('mhs_konfir_store');
		Route::post('/delete', 'KonfirmasiBayarController@delete')->name('mhs_konfir_delete');
		Route::get('/view-file/{file}', 'KonfirmasiBayarController@view')->name('mhs_konfir_view');

	});

	Route::group(['prefix' => 'mhs/bimbingan', 'namespace' => 'mahasiswa', 'middleware' => ['auth:web','minify']], function(){

		Route::get('/', 'BimbinganController@index')->name('mhs_bim');
		Route::get('/cetak/{id}', 'BimbinganController@cetak')->name('mhs_bim_cetak');
		Route::get('/filter', 'BimbinganController@setFilter')->name('mhs_bim_filter');
		Route::post('/upload-file', 'BimbinganController@uploadFile')->name('mhs_bim_upload');
		Route::post('/store-link', 'BimbinganController@storeLink')->name('mhs_bim_store_link');
		Route::get('/download-file', 'BimbinganController@download')->name('mhs_bim_download');
		Route::get('/download-lmpiran', 'BimbinganController@lampiran')->name('mhs_bim_lampiran');

	});

	Route::group(['prefix' => 'mhs/krs', 'namespace' => 'mahasiswa', 'middleware' => ['auth:web','minify']], function(){

		Route::get('/', 'KrsController@index')->name('mhs_krs');
		Route::get('/store-tmp', 'KrsController@storeTmp')->name('mhs_krs_store_tmp');
		Route::post('/store-tmp-arr', 'KrsController@storeTmpArr')->name('mhs_krs_store_tmp_arr');
		Route::get('/store', 'KrsController@store')->name('mhs_krs_store');
		Route::get('/cetak-krs', 'KrsController@cetakKrs')->name('mhs_krs_cetak');
		Route::get('/cetak-ksm', 'KrsController@cetakKsm')->name('mhs_ksm_cetak');
		Route::get('/delete-tmp/{id}', 'KrsController@deleteTmp')->name('mhs_krs_delete_tmp');

	});

	Route::group(['prefix' => 'mhs/pembayaran', 'namespace' => 'mahasiswa', 'middleware' => ['auth:web','minify','cors', 'role:mahasiswa|keuangan|admin']], function(){

		Route::get('/', 'KeuanganController@index')->name('mhs_pembayaran');
		Route::get('/token', 'KeuanganController@token')->name('mhs_token');
		Route::post('/bayar', 'KeuanganController@bayar')->name('mhs_bayar');
		Route::get('/cancel/{id}', 'KeuanganController@delete')->name('mhs_delete_briva');
		Route::get('/history-transaksi', 'KeuanganController@historyTransaksi')->name('mhs_history');
		Route::get('/push-notif', 'KeuanganController@pushNotif')->name('mhs_push_notif');

	});

	Route::group(['prefix' => 'mhs', 'namespace' => 'mahasiswa', 'middleware' => ['auth:web','minify','role:mahasiswa']], function(){
		Route::get('/rps', 'MhsController@rps')->name('mhs_rps');

		Route::get('/kartu-ujian', 'MhsController@kartuUjian')->name('mhs_kartu_ujian');
		
		Route::get('/profil', 'MhsController@profil')->name('mhs_profil');
		Route::post('/update', 'MhsController@updateProfil')->name('mhs_update_profil');
		Route::post('/update-foto', 'MhsController@updateFoto')->name('mhs_update_foto');
		Route::get('/update-akun', 'MhsController@updateAkun')->name('mhs_update_akun');

		Route::post('/store-dokumen', 'MhsController@dokumenStore')->name('mhs_doc_store');
		Route::get('/delete-dokumen/{id?}', 'MhsController@dokumenDelete')->name('mhs_doc_delete');
		Route::get('/ubah-judul-dokumen', 'MhsController@dokumenEdit')->name('mhs_doc_edit');
		Route::get('/download-dokumen', 'MhsController@dokumenDownload')->name('mhs_doc_download');
		
		Route::get('/jadwal-kuliah', 'MhsController@jadwalKuliah')->name('mhs_jdk');
		Route::get('/cetak-jadwal-kuliah', 'MhsController@jadwalKuliahCetak')->name('mhs_jdk_cetak');
		Route::get('/jadwal-ujian', 'MhsController@jadwalUjian')->name('mhs_jdu');
		Route::get('/cetak-jadwal-ujian', 'MhsController@jadwalUjianCetak')->name('mhs_jdu_cetak');
		Route::get('/khs', 'MhsController@khs')->name('mhs_khs');
		Route::get('/khs/cetak', 'MhsController@khsCetak')->name('mhs_khs_cetak');
		Route::get('/transkrip', 'MhsController@transkrip')->name('mhs_transkrip');
		Route::get('/transkrip-cetak', 'MhsController@transkripCetak')->name('mhs_transkrip_cetak');

		Route::get('/upload-jurnal', 'MhsController@jurnal')->name('mhs_jurnal');
		Route::post('/jurnal-store', 'MhsController@jurnalStore')->name('mhs_jurnal_store');
		Route::get('/jurnal-download', 'MhsController@jurnalDownload')->name('mhs_jurnal_download');
		Route::get('/jurnal-delete', 'MhsController@jurnalFileDelete')->name('mhs_jurnal_file_delete');
		
		Route::get('/konsentrasi', 'MhsController@pilihKonsentrasi')->name('mhs_konsentrasi');
		Route::post('/konsentrasi/store', 'MhsController@konsentrasiStore')->name('mhs_konsentrasi_store');
	});

	Route::group(['prefix' => 'mhs/lms', 'namespace' => 'mahasiswa', 'middleware' => ['auth:web','role:mahasiswa']], function(){
		Route::get('/', 'LmsController@index')->name('mhs_lms');
		Route::get('/detail/{id_jdk?}/{id_dosen?}', 'LmsController@detail')->name('mhs_lms_detail');
		
		Route::get('/get-jadwal', 'LmsController@getJadwal')->name('mhs_lms_get_jadwal');
		Route::get('/gabung/{id_jadwal?}', 'LmsController@gabung')->name('mhs_lms_gabung');
		Route::get('/batal-gabung/{id_peserta}/{id_jadwal}', 'LmsController@batalGabung')->name('mhs_lms_batal_gabung');
		
		Route::get('/materi/view/{id?}/{file?}', 'LmsController@materiPascaDownload')->name('mhs_lms_materi_pasca_download');
		
		Route::get('/materi/view/{id_materi?}/{id_dosen?}/{file?}', 'LmsController@materiView')->name('mhs_lms_materi_view');
		Route::get('/tugas/detail/{id_jdk?}/{id_tugas?}/{id_dosen?}', 'LmsController@tugasDetail')->name('mhs_lms_tugas_detail');
		Route::post('/tugas/store', 'LmsController@tugasStore')->name('mhs_lms_tugas_store');
		Route::post('/tugas/update', 'LmsController@tugasUpdate')->name('mhs_lms_tugas_update');
		Route::get('/tugas/download/{id_tugas}/{file}', 'LmsController@tugasDownload')->name('mhs_lms_tugas_download');
		Route::get('/lms/tugas/view-attach/{id_tugas}/{id_dosen}/{file}', 'LmsController@tugasViewAttach')->name('mhs_lms_tugas_view_att');
	
		Route::get('/topik/get', 'LmsController@lmsTopik')->name('mhs_lms_topik');
		Route::get('/topik/detail/{id}', 'LmsController@lmsTopikDetail')->name('mhs_lms_topik_detail');
		Route::post('/topik/store', 'LmsController@lmsTopikStore')->name('mhs_lms_topik_store');
		Route::get('/topik/edit/{id?}', 'LmsController@lmsTopikEdit')->name('mhs_lms_topik_edit');
		Route::post('/topik/update', 'LmsController@lmsTopikUpdate')->name('mhs_lms_topik_update');
		Route::post('/topik/reply/{id}', 'LmsController@lmsTopikReply')->name('mhs_lms_topik_reply');
		Route::post('/topik/reply-update', 'LmsController@lmsTopikReplyUpdate')->name('mhs_lms_topik_reply_update');
		Route::get('/topik/delete/{id}/{id_dosen}', 'LmsController@lmsTopikDelete')->name('mhs_lms_topik_delete');
		Route::get('/topik/delete-reply/{id}/{id_topik}/{deleted}/{id_dosen}', 'LmsController@lmsTopikReplyToggleDelete')->name('mhs_lms_topik_reply_toggle_delete');
		Route::get('/topik/tutup/{id_topik}/{id_dosen}', 'LmsController@lmsTopikTutup')->name('mhs_lms_topik_tutup');
		
		Route::get('/kuis/detail/{id_jdk}/{id_kuis}/{id_dosen?}', 'KuisController@index')->name('mhs_kuis');
		Route::get('/kuis/kerja/{id_jdk}/{id_kuis}/{id_dosen?}', 'KuisController@kerja')->name('mhs_kuis_kerja');
		Route::get('/kuis/update-waktu', 'KuisController@updateWaktu')->name('mhs_kuis_update_waktu');
		Route::post('/kuis/store', 'KuisController@store')->name('mhs_kuis_store');
		Route::post('/kuis/store-single', 'KuisController@storeSingle')->name('mhs_kuis_store_single');
	
		Route::get('/video/detail/{id_jdk}/{id_video}/{id_dosen?}', 'LmsController@video')->name('mhs_video');
		Route::get('/video/update-ketersediaan', 'LmsController@videoUpdateKetersediaan')->name('mhs_video_update_ketersediaan');
	});

	Route::group(['prefix' => 'mhs/kuesioner', 'namespace' => 'mahasiswa', 'middleware' => ['auth:web','minify']], function(){

		Route::get('/', 'KuesionerController@index')->name('mhs_kues');
		Route::get('/add', 'KuesionerController@add')->name('mhs_kues_add');
		Route::post('/store', 'KuesionerController@store')->name('mhs_kues_store');
	});

	Route::group(['prefix' => 'mhs/pkm', 'namespace' => 'mahasiswa', 'middleware' => 'auth:web'], function(){
		Route::get('/', 'PkmController@index')->name('m_pkm');
		Route::post('/cari', 'PkmController@cari')->name('m_pkm_cari');
		Route::get('/set-filter', 'PkmController@setFilter')->name('m_pkm_filter');
		Route::get('/get-mahasiswa', 'PkmController@getMahasiswa')->name('pkm_get_mahasiswa');
		Route::get('/get-dosen', 'PkmController@getDosen')->name('pkm_get_dosen');
		Route::get('/daftar', 'PkmController@daftar')->name('m_pkm_daftar');
		Route::get('/edit', 'PkmController@edit')->name('m_pkm_edit');
		Route::post('/update', 'PkmController@update')->name('m_pkm_update');
		Route::post('/store', 'PkmController@store')->name('m_pkm_store');
		Route::post('/anggota-store', 'PkmController@anggotaStore')->name('m_pkm_anggota_store');
		Route::get('/anggota-delete', 'PkmController@anggotaDelete')->name('m_pkm_anggota_delete');
		Route::post('/dosen-store', 'PkmController@dosenStore')->name('m_pkm_dosen_store');
		Route::get('/dosen-delete', 'PkmController@dosenDelete')->name('m_pkm_dosen_delete');
		Route::get('/detail/{id?}', 'PkmController@detail')->name('m_pkm_detail');
	});

	Route::group(['prefix' => 'mhs/daftar-seminar', 'namespace' => 'mahasiswa', 'middleware' => 'auth:web'], function(){
		Route::get('/', 'SeminarController@index')->name('mhs_seminar');
		Route::get('/add', 'SeminarController@add')->name('mhs_seminar_add');
		Route::post('/store', 'SeminarController@store')->name('mhs_seminar_store');
		Route::post('/store-ajuan', 'SeminarController@storeAjuan')->name('mhs_seminar_store_ajuan');
		Route::post('/delete-file', 'SeminarController@deleteFile')->name('mhs_seminar_delete_file');
	});

	Route::group(['prefix' => 'mhs/pendaftaran-ijazah', 'namespace' => 'mahasiswa', 'middleware' => 'auth:web'], function(){
		Route::get('/', 'PendaftaranIjazahController@index')->name('daftar_ijazah');
		Route::get('/set-filter', 'PendaftaranIjazahController@setFilter')->name('daftar_ijazah_filter');
		Route::post('/store', 'PendaftaranIjazahController@store')->name('daftar_ijazah_store');
		Route::get('/download', 'PendaftaranIjazahController@download')->name('daftar_ijazah_download');
	});

	Route::group(['prefix' => 'mhs/absensi', 'namespace' => 'mahasiswa', 'middleware' => 'auth:web'], function(){
		Route::get('/', 'AbsenMhsController@index')->name('mhs_absensi');
		Route::get('/detail', 'AbsenMhsController@detail')->name('mhs_absensi_detail');
		Route::post('/store', 'AbsenMhsController@store')->name('mhs_absensi_store');
	});

/* Dosen member */
	Route::group(['prefix' => 'dsn/bimbingan', 'namespace' => 'dosen', 'middleware' => ['auth:web','role:dosen']], function(){

		Route::get('/', 'BimbinganController@index')->name('dsn_bim');
		Route::get('/detail/{id_mhs_reg}/{id_smt}', 'BimbinganController@detail')->name('dsn_bim_detail');
		Route::get('/add/komentar/{id}/{id_mhs_reg}', 'BimbinganController@addKomentar')->name('dsn_bim_add_komentar');
		Route::post('/detail/komentar', 'BimbinganController@storeKomentar')->name('dsn_bim_komentar');
		Route::get('/edit/komentar/{id}', 'BimbinganController@editKomentar')->name('dsn_bim_edit_komentar');
		Route::get('/delete/lampiran/{id}', 'BimbinganController@deleteLampiran')->name('dsn_bim_delete_lampiran');
		Route::post('/update/komentar', 'BimbinganController@updateKomentar')->name('dsn_bim_update_komentar');
		Route::get('/delete/{id}', 'BimbinganController@delete')->name('dsn_bim_delete');
		Route::get('/filter', 'BimbinganController@setFilter')->name('dsn_bim_filter');
		Route::post('/cari', 'BimbinganController@cari')->name('dsn_bim_cari');
		Route::get('/download-file', 'BimbinganController@download')->name('dsn_bim_download');
		Route::get('/download-lmpiran', 'BimbinganController@lampiran')->name('dsn_bim_lampiran');
		Route::post('/selesai', 'BimbinganController@selesai')->name('dsn_bim_selesai');

	});

  Route::group(['prefix' => 'dsn/pgj', 'namespace' => 'dosen', 'middleware' => ['auth:web', 'role:dosen']], function(){
    Route::get('/', 'PengujianController@index')->name('dsn_pgj');
    Route::get('/filter', 'PengujianController@setFilter')->name('dsn_pgj_filter');
  });

	Route::group(['prefix' => 'dsn/nilai-seminar', 'namespace' => 'dosen', 'middleware' => ['auth:web','role:dosen']], function(){
		Route::get('/', 'NilaiUjianSeminarController@index')->name('dsn_seminar');
		Route::get('/detail/{id_mhs_reg}/{id_smt}', 'NilaiUjianSeminarController@detail')->name('dsn_seminar_detail');
		Route::get('/cetak/{id_mhs_reg}/{id_smt}', 'NilaiUjianSeminarController@cetak')->name('dsn_seminar_cetak');
		Route::get('/nilai', 'NilaiUjianSeminarController@nilai')->name('dsn_seminar_nilai');
		Route::post('/store', 'NilaiUjianSeminarController@store')->name('dsn_seminar_store');
		Route::get('/filter', 'NilaiUjianSeminarController@setFilter')->name('dsn_seminar_filter');
		Route::post('/cari', 'NilaiUjianSeminarController@cari')->name('dsn_seminar_cari');
	});

	Route::group(['prefix' => 'dsn', 'namespace' => 'dosen', 'middleware' => ['auth:web','role:dosen|ketua 1|admin']], function(){

		Route::get('/rps', 'DsnController@rps')->name('dsn_rps');
		Route::get('/jadwal', 'DsnController@jadwal')->name('dsn_jadwal');
		Route::get('/jadwal-filter', 'DsnController@jadwalFilter')->name('dsn_jadwal_filter');
		Route::get('/jadwal-cetak', 'DsnController@jadwalCetak')->name('dsn_jadwal_cetak');
		Route::get('/profil', 'DsnController@profil')->name('dsn_profil');
		Route::post('/update-foto', 'DsnController@updateFoto')->name('dsn_update_foto');
		Route::post('/ttd', 'DsnController@ttd')->name('dsn_ttd');
		Route::post('/update-profil', 'DsnController@updateProfil')->name('dsn_update_profil');
		Route::get('/hasil-kuesioner', 'DsnController@detailKuesioner')->name('dsn_kuesioner_detail');
		Route::get('/nilai/{id}', 'DsnController@nilai')->name('dsn_nilai');
		Route::get('/nilai/cetak/{id}', 'DsnController@nilaiCetak')->name('dsn_nilai_cetak');
		Route::get('/nilai/cetak-s2/{id}', 'DsnController@nilaiCetakS2')->name('dsn_nilai_cetak_s2');
		Route::get('/nilai/ekspor/{id}', 'DsnController@nilaiEkspor')->name('dsn_nilai_ekspor');
		Route::get('/nilai-hitung', 'DsnController@cekNilaiAkhir')->name('dsn_nilai_akhir');
		Route::post('/update-nilai', 'DsnController@nilaiUpdate')->name('dsn_nilai_update');
		Route::post('/update-nilai-s2', 'DsnController@nilaiUpdateS2')->name('dsn_nilai_update_s2');
		Route::post('/update-nilai-s2-single', 'DsnController@nilaiUpdateS2Single')->name('dsn_nilai_update_s2_single');
		Route::get('/absen/{id}', 'DsnController@absen')->name('dsn_absen');
		Route::get('/absen-mhs/store', 'DsnController@absenStoreMhs')->name('dsn_store_absen_mhs');
		Route::get('/absen-mhs-cetak/{id}', 'DsnController@absenMhsCetak')->name('dsn_absen_mhs_cetak');
		Route::get('/absen-dsn/store', 'DsnController@absenStoreDsn')->name('dsn_store_absen_dsn');
		Route::get('/absen-dsn-cetak/{id}', 'DsnController@absenDsnCetak')->name('dsn_absen_dsn_cetak');

		Route::post('/buka-absen', 'DsnController@absenBuka')->name('dsn_absen_buka');
		
		// E-learning
		Route::get('/lms/{id}', 'DsnController@lms')->name('dsn_lms');
		Route::post('/lms/upload/file', 'DsnController@lmsUploadFile')->name('dsn_lms_upload_file');
		Route::post('/lms/upload/materi', 'DsnController@lmsUploadMateri')->name('dsn_lms_upload_materi');
		Route::get('/lms/delete/resources', 'DsnController@lmsDeleteResources')->name('dsn_lms_delete_resources');
		Route::post('/lms/urutan/update', 'DsnController@lmsUpdateUrutan')->name('dsn_lms_update_urutan');
		Route::get('/lms/pindah/pertemuan', 'DsnController@lmsPindahPertemuan')->name('dsn_lms_pindah_pertemuan');
		
		Route::get('/lms/get/mhs', 'DsnController@getMhs')->name('dsn_lms_get_mhs');
		Route::get('/lms/undang-mhs/{id_jadwal?}/{id_peserta?}', 'DsnController@undangMhs')->name('dsn_lms_undang_mhs');
		Route::get('/lms/approval/{id_jadwal?}/{id_peserta?}', 'DsnController@approvalMhs')->name('dsn_lms_approval_mhs');

		Route::get('/lms/materi/add/{id}', 'DsnController@lmsMateriAdd')->name('dsn_lms_add_materi');
		Route::post('/lms/materi/store', 'DsnController@lmsMateriStore')->name('dsn_lms_materi_store');
		Route::get('/lms/materi/edit/{id}', 'DsnController@lmsMateriEdit')->name('dsn_lms_materi_edit');
		Route::post('/lms/materi/update', 'DsnController@lmsMateriUpdate')->name('dsn_lms_materi_update');
		Route::get('/lms/materi/view/{id_materi?}/{id_dosen?}/{file?}', 'DsnController@lmsMateriView')->name('dsn_lms_materi_view');
		Route::post('/lms/upload/tmp', 'DsnController@lmsUploadTmp')->name('dsn_lms_upload_tmp');

		Route::get('/lms/tugas/add/{id}', 'DsnController@lmsTugasAdd')->name('dsn_lms_tugas_add');
		Route::post('/lms/tugas/store', 'DsnController@lmsTugasStore')->name('dsn_lms_tugas_store');
		Route::get('/lms/tugas/edit/{id}', 'DsnController@lmsTugasEdit')->name('dsn_lms_tugas_edit');
		Route::post('/lms/tugas/update', 'DsnController@lmsTugasUpdate')->name('dsn_lms_tugas_update');
		Route::get('/lms/tugas/detail/{id_jadwal}/{id}', 'DsnController@lmsTugasDetail')->name('dsn_lms_tugas_detail');
		Route::post('/lms/tugas/grade', 'DsnController@lmsTugasGrade')->name('dsn_lms_tugas_grade');
		Route::get('/lms/tugas/download-all', 'DsnController@lmsTugasDownload')->name('dsn_lms_tugas_download');
		Route::get('/lms/tugas/download-single/{id}', 'DsnController@lmsTugasDownloadSingle')->name('dsn_lms_tugas_download_single');
		Route::get('/lms/tugas/jawaban', 'DsnController@lmsTugasJawaban')->name('dsn_lms_tugas_jawaban');
		Route::get('/lms/tugas/view-attach/{id_tugas}/{id_dosen}', 'DsnController@lmsTugasViewAttach')->name('dsn_lms_tugas_view_att');

		Route::get('/lms/catatan/add/{id}', 'DsnController@lmsCatatanAdd')->name('dsn_lms_catatan_add');
		Route::post('/lms/catatan/store', 'DsnController@lmsCatatanStore')->name('dsn_lms_catatan_store');
		Route::get('/lms/catatan/edit/{id}/{id_catatan}', 'DsnController@lmsCatatanEdit')->name('dsn_lms_catatan_edit');
		Route::post('/lms/catatan/update/{id}', 'DsnController@lmsCatatanUpdate')->name('dsn_lms_catatan_update');

		Route::get('/lms/topik/get', 'DsnController@lmsTopik')->name('dsn_lms_topik');
		Route::get('/lms/topik/detail/{id}', 'DsnController@lmsTopikDetail')->name('dsn_lms_topik_detail');
		Route::post('/lms/topik/store', 'DsnController@lmsTopikStore')->name('dsn_lms_topik_store');
		Route::get('/lms/topik/edit/{id?}', 'DsnController@lmsTopikEdit')->name('dsn_lms_topik_edit');
		Route::post('/lms/topik/update', 'DsnController@lmsTopikUpdate')->name('dsn_lms_topik_update');
		Route::post('/lms/topik/reply/{id}', 'DsnController@lmsTopikReply')->name('dsn_lms_topik_reply');
		Route::post('/lms/topik/reply-update', 'DsnController@lmsTopikReplyUpdate')->name('dsn_lms_topik_reply_update');
		Route::get('/lms/topik/delete/{id}/{id_dosen}', 'DsnController@lmsTopikDelete')->name('dsn_lms_topik_delete');
		Route::get('/lms/topik/delete-reply/{id}/{id_topik}/{deleted}/{id_dosen}', 'DsnController@lmsTopikReplyToggleDelete')->name('dsn_lms_topik_reply_toggle_delete');
		Route::get('/lms/topik/tutup/{id_topik}/{id_dosen}', 'DsnController@lmsTopikTutup')->name('dsn_lms_topik_tutup');
		
		Route::get('/lms/kuis/add/{id}', 'KuisController@add')->name('kuis_add');
		Route::get('/lms/kuis/add/soal/{id?}/{id_kuis?}', 'KuisController@addSoal')->name('kuis_add_soal');
		Route::get('/lms/kuis/edit/soal/{id?}/{id_kuis?}/{id_soal?}', 'KuisController@editSoal')->name('kuis_edit_soal');
		Route::post('/lms/kuis/update/soal', 'KuisController@updateSoal')->name('kuis_update_soal');
		Route::get('/lms/kuis/delete/soal/{id_kuis_soal}/{id_jadwal}', 'KuisController@deleteSoal')->name('kuis_delete_soal');
		Route::post('/lms/kuis/store', 'KuisController@store')->name('kuis_store');
		Route::get('/lms/kuis/bank-soal', 'KuisController@bankSoal')->name('kuis_get_bank_soal');
		Route::get('/lms/kuis/ambil-soal/{id_soal}/{id_kuis}', 'KuisController@ambilSoal')->name('kuis_ambil_soal');
		Route::post('/lms/kuis/soal/store', 'KuisController@soalStore')->name('kuis_soal_store');
		Route::get('/lms/kuis/edit/{id}/{id_kuis}', 'KuisController@edit')->name('kuis_edit');
		Route::post('/lms/kuis/update', 'KuisController@update')->name('kuis_update');
		Route::get('/lms/kuis/delete/{id}', 'KuisController@delete')->name('kuis_delete');
		Route::get('/lms/kuis/detail/{id_jadwal?}/{id?}', 'KuisController@detail')->name('kuis_detail');
		Route::get('/lms/kuis/jawaban/{id_jadwal}/{id}', 'KuisController@jawaban')->name('kuis_jawaban');
		Route::get('/lms/kuis/jawaban/detail/{id_jadwal}/{id}/{id_peserta}', 'KuisController@jawabanDetail')->name('kuis_jawaban_detail');
		Route::post('/lms/kuis/jawaban/grade/{id_jadwal?}/{id_peserta?}', 'KuisController@grade')->name('kuis_grade');

		Route::get('/lms/video/add/{id}', 'VideoController@add')->name('video_add');
		Route::get('/lms/video/detail/{id}/{id_video}', 'VideoController@detail')->name('video_detail');
		Route::post('/lms/video/upload', 'VideoController@upload')->name('video_upload');
		Route::post('/lms/video/store', 'VideoController@store')->name('video_store');
		Route::get('/lms/video/cek', 'VideoController@cekVideoId')->name('video_cek_id');
		Route::get('/lms/video/edit/{id}/{id_video}', 'VideoController@edit')->name('video_edit');
		Route::post('/lms/video/update', 'VideoController@update')->name('video_update');
		Route::get('/lms/video/delete/{id}/{id_video}', 'VideoController@delete')->name('video_delete');
		Route::get('/lms/video/delete-tmp', 'VideoController@deleteTmp')->name('video_delete_tmp');
		Route::get('/lms/video/update-ketersediaan', 'VideoController@updateKetersediaanVideo')->name('video_update_ketersediaan');

		Route::get('/lms/bank-soal/add', 'BankSoalController@add')->name('bs_add');
		Route::post('/lms/bank-soal/store', 'BankSoalController@store')->name('bs_store');
		Route::get('/lms/bank-soal/edit', 'BankSoalController@edit')->name('bs_edit');
		Route::post('/lms/bank-soal/update', 'BankSoalController@update')->name('bs_update');
		Route::get('/lms/bank-soal/detail', 'BankSoalController@detail')->name('bs_detail');

		Route::get('/filemanager', 'DsnController@filemanager')->name('dsn_fm');
		Route::post('/filemanager/store', 'DsnController@fmStore')->name('dsn_fm_store');
		Route::get('/filemanager/delete/{id?}', 'DsnController@fmDelete')->name('dsn_fm_delete');
		Route::post('/filemanager/update', 'DsnController@fmUpdate')->name('dsn_fm_update');

		Route::group(['prefix' => 'kegiatan'], function(){
			Route::get('/', 'KegiatanController@index')->name('dsn_kegiatan');
			Route::post('/store', 'KegiatanController@store')->name('dsn_kegiatan_store');
			Route::get('/edit/{id?}', 'KegiatanController@edit')->name('dsn_kegiatan_edit');
			Route::post('/update', 'KegiatanController@update')->name('dsn_kegiatan_update');
			Route::get('/filter', 'KegiatanController@filter')->name('dsn_kegiatan_filter');
			Route::get('/view/{id}/{id_dosen}/{file}', 'KegiatanController@viewDok')->name('dsn_kegiatan_viewdok');
			Route::get('/download/{id_dosen}', 'KegiatanController@download')->name('dsn_kegiatan_download');
			Route::get('/delete/{id}', 'KegiatanController@delete')->name('dsn_kegiatan_delete');
		});

		Route::group(['prefix' => 'seminar'], function(){

			Route::get('/', 'DsnController@seminar')->name('dsn_approv_seminar');
			Route::get('/set-filter', 'DsnController@setFilter')->name('dsn_approv_seminar_filter');
			Route::post('/update', 'DsnController@seminarUpdate')->name('dsn_approv_seminar_update');
			Route::get('/detail/{id}', 'DsnController@seminarDetail')->name('dsn_approv_seminar_detail');
		});

	});

	Route::group(['prefix' => 'dsn/sk', 'namespace' => 'dosen', 'middleware' => ['auth:web','role:dosen']], function(){
		Route::get('/mengajar', 'DsnController@skMengajar')->name('dsn_sk_mengajar');
		Route::get('/bimbingan-data', 'DsnController@skBimbinganData')->name('dsn_sk_bimbingan_data');
		Route::post('/bimbingan', 'DsnController@skBimbingan')->name('dsn_sk_bimbingan');
    Route::get('/pengujian-data', 'PengujianController@skPengujianData')->name('dsn_sk_pengujian_data');
    Route::post('/pengujian-data', 'PengujianController@skPengujian')->name('dsn_sk_pengujian');
	});
