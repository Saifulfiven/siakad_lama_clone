<?php

namespace App\Imports;


use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
class MahasiswaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        Log::error($row);
        return new Mahasiswa([
            'name' => $row['name'],
            'email' => $row['email'],

        ]);
    }
}
