<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KuisHasil extends Model
{
    protected $table = 'lmsk_kuis_hasil';

    protected $fillable = ['id_peserta', 'id_kuis_soal', 'jawaban', 'penilaian', 'komentar_pengajar'];
}
