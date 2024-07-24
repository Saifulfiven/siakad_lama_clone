<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mahasiswareg extends Model
{
	protected $table = 'mahasiswa_reg';
  
	public $incrementing = false;

	protected $dates = ['tgl_masuk','tgl_keluar'];

    public function potongan()
    {
    	return $this->hasOne('App\PotonganBiayaKuliah', 'id_mhs_reg');
    }

    public function mhs()
    {
    	return $this->belongsTo('App\Mahasiswa', 'id_mhs');
    }

    public function prodi()
    {
        return $this->belongsTo('App\Prodi', 'id_prodi', 'id_prodi');
    }

    public function dosenWali()
    {
        return $this->belongsTo('App\Dosen', 'dosen_pa','id');
    }

    public function konsentrasi()
    {
        return $this->belongsTo('App\Konsentrasi', 'id_konsentrasi', 'id_konsentrasi');
    }

    public function semester(){
        return $this->belongsTo('App\Semester','semester_mulai','id_smt');
    }
}
