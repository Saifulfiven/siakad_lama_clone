<?php


Route::group(['middleware' => 'web'], function(){
	Route::get('/', function(){
		dd('ok');
	});

	Route::group(['prefix' => 'lulus-keluar'], function(){
		Route::get('/', 'LulusKeluarController@index')->name('fdr_lk');
		Route::get('/data', 'LulusKeluarController@data')->name('fdr_lk_data');
		Route::get('/filter', 'LulusKeluarController@setFilter')->name('fdr_lk_filter');
		Route::get('/reset-filter', 'LulusKeluarController@resetFilter')->name('fdr_lk_resetfilter');
		Route::get('/cari', 'LulusKeluarController@cari')->name('fdr_lk_cari');
		Route::get('/ekspor', 'LulusKeluarController@ekspor')->name('fdr_lk_ekspor');
	});

});