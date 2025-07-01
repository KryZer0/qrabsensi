<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class absenModel extends Model
{
    protected $table = 'absensi';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nisn', 'tanggal', 'keterangan', 'jam_masuk', 'jam_keluar'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function siswa()
    {
        return $this->belongsTo(siswaModel::class, 'nisn', 'nisn');
    }

}