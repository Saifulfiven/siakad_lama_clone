<?php

Route::group(['middleware' => 'cors'], function(){
	Route::get('/tugas-detail', 'MobileController@tugas');
	Route::post('/tugas-store', 'MobileController@tugasStore')->name('m_lms_tugas_store');
	Route::post('/tugas-update', 'MobileController@tugasUpdate')->name('m_lms_tugas_update');
	Route::get('/tugas-download/{id_tugas}/{file}', 'MobileController@tugasDownload')->name('m_lms_tugas_download');
	Route::get('/tugas-view-attach/{id_tugas}/{id_dosen}/{file}', 'MobileController@tugasViewAttach')->name('m_lms_tugas_view_attach');
	
	Route::get('/kuis-detail', 'MobileController@kuis')->name('kuis');
	Route::get('/kuis-update-waktu', 'MobileController@kuisUpdateWaktu')->name('m_kuis_update_waktu');
	Route::get('/kuis-kerja', 'MobileController@kerjaKuis')->name('m_kerja_kuis');
	Route::post('/kuis-store', 'MobileController@kuisStore')->name('m_mhs_kuis_store');
	Route::post('/kuis-store-single', 'MobileController@kuisStoreSingle')->name('m_kuis_store_single');
	Route::get('/video', 'MobileController@video')->name('video');

	Route::get('/dsn/lms', 'MobileController@lmsDsn')->name('dsnm_lms');
	Route::post('/lms/upload/materi', 'MobileController@lmsUploadMateri')->name('dsnm_lms_upload_materi');
	Route::get('/lms/delete/resources', 'MobileController@lmsDeleteResources')->name('dsnm_lms_delete_resources');
	Route::post('/lms/urutan/update', 'MobileController@lmsUpdateUrutan')->name('dsnm_lms_update_urutan');
	Route::get('/lms/pindah/pertemuan', 'MobileController@lmsPindahPertemuan')->name('dsnm_lms_pindah_pertemuan');
	
	Route::get('/lms/get/mhs', 'MobileController@getMhs')->name('dsnm_lms_get_mhs');
	Route::get('/lms/undang-mhs/{id_jadwal?}/{id_peserta?}', 'MobileController@undangMhs')->name('dsnm_lms_undang_mhs');
	Route::get('/lms/approval/{id_jadwal?}/{id_peserta?}', 'MobileController@approvalMhs')->name('dsnm_lms_approval_mhs');

	Route::get('/lms/materi/add/{id?}', 'MobileController@lmsMateriAdd')->name('dsnm_lms_add_materi');
	Route::post('/lms/materi/store', 'MobileController@lmsMateriStore')->name('dsnm_lms_materi_store');
	Route::get('/lms/materi/edit/{id}', 'MobileController@lmsMateriEdit')->name('dsnm_lms_materi_edit');
	Route::post('/lms/materi/update', 'MobileController@lmsMateriUpdate')->name('dsnm_lms_materi_update');
	Route::get('/lms/materi/view/{id_materi?}/{id_dosen?}/{file?}', 'MobileController@lmsMateriView')->name('dsnm_lms_materi_view');
	Route::post('/lms/upload/tmp', 'MobileController@lmsUploadTmp')->name('dsnm_lms_upload_tmp');

	Route::get('/lms/tugas/add/{id?}', 'MobileController@lmsTugasAdd')->name('dsnm_lms_tugas_add');
	Route::post('/lms/tugas/store', 'MobileController@lmsTugasStore')->name('dsnm_lms_tugas_store');
	Route::get('/lms/tugas/edit/{id}', 'MobileController@lmsTugasEdit')->name('dsnm_lms_tugas_edit');
	Route::post('/lms/tugas/update', 'MobileController@lmsTugasUpdate')->name('dsnm_lms_tugas_update');
	Route::get('/lms/tugas/detail/{id_jadwal}/{id}', 'MobileController@lmsTugasDetail')->name('dsnm_lms_tugas_detail');
	Route::post('/lms/tugas/grade', 'MobileController@lmsTugasGrade')->name('dsnm_lms_tugas_grade');
	Route::get('/lms/tugas/download-all', 'MobileController@lmsTugasDownload')->name('dsnm_lms_tugas_download');
	Route::get('/lms/tugas/download-single/{id}', 'MobileController@lmsTugasDownloadSingle')->name('dsnm_lms_tugas_download_single');
	Route::get('/lms/tugas/jawaban', 'MobileController@lmsTugasJawaban')->name('dsnm_lms_tugas_jawaban');

	Route::get('/lms/kuis/add/{id?}', 'MobileController@m_add')->name('m_kuis_add');
	Route::get('/lms/kuis/add/soal/{id?}/{id_kuis?}', 'MobileController@m_addSoal')->name('m_kuis_add_soal');
	Route::get('/lms/kuis/edit/soal/{id?}/{id_kuis?}/{id_soal?}', 'MobileController@m_editSoal')->name('m_kuis_edit_soal');
	Route::post('/lms/kuis/update/soal', 'MobileController@m_updateSoal')->name('m_kuis_update_soal');
	Route::get('/lms/kuis/delete/soal/{id_kuis_soal}/{id_jadwal}', 'MobileController@m_deleteSoal')->name('m_kuis_delete_soal');
	Route::post('/lms/kuis/store', 'MobileController@m_store')->name('m_kuis_store');
	Route::get('/lms/kuis/bank-soal', 'MobileController@m_bankSoal')->name('m_kuis_get_bank_soal');
	Route::get('/lms/kuis/ambil-soal/{id_soal}/{id_kuis}', 'MobileController@m_ambilSoal')->name('m_kuis_ambil_soal');
	Route::post('/lms/kuis/soal/store', 'MobileController@m_soalStore')->name('m_kuis_soal_store');
	Route::get('/lms/kuis/edit/{id}/{id_kuis}', 'MobileController@m_edit')->name('m_kuis_edit');
	Route::post('/lms/kuis/update', 'MobileController@m_update')->name('m_kuis_update');
	Route::get('/lms/kuis/delete/{id}', 'MobileController@m_delete')->name('m_kuis_delete');
	Route::get('/lms/kuis/detail/{id_jadwal?}/{id?}', 'MobileController@m_detail')->name('m_kuis_detail');
	Route::get('/lms/kuis/jawaban/{id_jadwal}/{id}', 'MobileController@m_jawaban')->name('m_kuis_jawaban');
	Route::get('/lms/kuis/jawaban/detail/{id_jadwal}/{id}/{id_peserta}', 'MobileController@m_jawabanDetail')->name('m_kuis_jawaban_detail');
	Route::post('/lms/kuis/jawaban/grade/{id_jadwal?}/{id_peserta?}', 'MobileController@m_grade')->name('m_kuis_grade');

	Route::get('/lms/video/add/{id?}', 'VideoController@add')->name('m_video_add');
	Route::get('/lms/video/detail/{id}/{id_video}', 'VideoController@detail')->name('m_video_detail');
	Route::post('/lms/video/upload', 'VideoController@upload')->name('m_video_upload');
	Route::post('/lms/video/store', 'VideoController@store')->name('m_video_store');
	Route::get('/lms/video/edit/{id}/{id_video}', 'VideoController@edit')->name('m_video_edit');
	Route::post('/lms/video/update', 'VideoController@update')->name('m_video_update');
	Route::get('/lms/video/delete/{id}/{id_video}', 'VideoController@delete')->name('m_video_delete');
	Route::get('/lms/video/delete-tmp', 'VideoController@deleteTmp')->name('m_video_delete_tmp');
	Route::get('/lms/video/update-ketersediaan', 'VideoController@updateKetersediaanVideo')->name('m_video_update_ketersediaan');

	Route::get('/lms/catatan/add/{id?}', 'MobileController@lmsCatatanAdd')->name('dsnm_lms_catatan_add');
	Route::post('/lms/catatan/store', 'MobileController@lmsCatatanStore')->name('dsnm_lms_catatan_store');
	Route::get('/lms/catatan/edit/{id}', 'MobileController@lmsCatatanEdit')->name('dsnm_lms_catatan_edit');
	Route::post('/lms/catatan/update/{id}', 'MobileController@lmsCatatanUpdate')->name('dsnm_lms_catatan_update');

	Route::get('/lms/topik/get', 'MobileController@lmsTopik')->name('dsnm_lms_topik');
	Route::get('/lms/topik/detail/{id}', 'MobileController@lmsTopikDetail')->name('dsnm_lms_topik_detail');
	Route::post('/lms/topik/store', 'MobileController@lmsTopikStore')->name('dsnm_lms_topik_store');
	Route::get('/lms/topik/edit/{id?}', 'MobileController@lmsTopikEdit')->name('dsnm_lms_topik_edit');
	Route::post('/lms/topik/update', 'MobileController@lmsTopikUpdate')->name('dsnm_lms_topik_update');
	Route::post('/lms/topik/reply/{id}', 'MobileController@lmsTopikReply')->name('dsnm_lms_topik_reply');
	Route::post('/lms/topik/reply-update', 'MobileController@lmsTopikReplyUpdate')->name('dsnm_lms_topik_reply_update');
	Route::get('/lms/topik/delete/{id}/{id_dosen}', 'MobileController@lmsTopikDelete')->name('dsnm_lms_topik_delete');
	Route::get('/lms/topik/delete-reply/{id}/{id_topik}/{deleted}/{id_dosen}', 'MobileController@lmsTopikReplyToggleDelete')->name('dsnm_lms_topik_reply_toggle_delete');
	Route::get('/lms/topik/tutup/{id_topik}/{id_dosen}', 'MobileController@lmsTopikTutup')->name('dsnm_lms_topik_tutup');


	Route::get('/nilai', 'MobileController@nilai');
	Route::post('/nilai-update', 'MobileController@nilaiUpdates2')->name('m_nilai_updates2');
	Route::post('/nilai-update-single', 'MobileController@nilaiUpdates2Single')->name('m_nilai_updates2_single');

	Route::get('/dsn/bimbingan/{id_dosen}/{id_mhs_reg}/{id_smt}', 'BimbinganController@index')->name('m_dsn_bimbingan');
	Route::get('/dsn/bimbingan-add/{id}/{id_mhs_reg}/{id_dosen}', 'BimbinganController@addKomentar')->name('m_dsn_bimbingan_add');
	Route::post('/dsn/bimbingan-store', 'BimbinganController@storeKomentar')->name('m_dsn_bimbingan_store');
	Route::get('/dsn/bimbingan-edit/{id}/{id_dosen}', 'BimbinganController@editKomentar')->name('m_dsn_bimbingan_edit');
	Route::get('/delete/lampiran/{id}', 'BimbinganController@deleteLampiran')->name('m_dsn_bim_delete_lampiran');
	Route::post('/dsn/bimbingan-update', 'BimbinganController@updateKomentar')->name('m_dsn_bimbingan_update');
	Route::get('/dsn/bimbingan-delete/{id}/{id_dosen}/{jenis}', 'BimbinganController@delete')->name('m_dsn_bimbingan_delete');
	Route::get('/download-file', 'BimbinganController@download')->name('m_dsn_bim_download');
	Route::get('/download-lmpiran', 'BimbinganController@lampiran')->name('m_dsn_bim_lampiran');
	Route::post('/selesai', 'BimbinganController@selesai')->name('m_dsn_bim_selesai');


	Route::get('/mhs/profil/{nim}', 'MobileController@profilMhs')->name('m_mhs_profil');
	Route::post('/mhs/profil/update', 'MobileController@updateProfilMhs')->name('m_mhs_update_profil');

});