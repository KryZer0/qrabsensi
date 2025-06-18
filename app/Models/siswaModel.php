<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class siswaModel extends Model
{
    protected $table = 'siswa';
    protected $fillable = [
        'nisn',
        'nama',
        'jns_kelamin',
        'kelas',
        'jurusan',
        'id_wali',
    ];

    public function siswa()
    {
        return $this->belongsTo('App\Models\siswaModel');
    }
}
