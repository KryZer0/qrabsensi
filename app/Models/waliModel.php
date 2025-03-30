<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class waliModel extends Model
{
    //
    protected $table = 'wali_siswa';
    protected $fillable = [
        'nama',
    ];

    public function wali()
    {
        return $this->belongsTo('App\Models\waliModel');
    }
}
