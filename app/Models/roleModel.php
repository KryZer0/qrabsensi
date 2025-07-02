<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class roleModel extends Model
{
    protected $table = 'tb_role';
    protected $primaryKey = 'id_role';
    protected $fillable = [
        'id_role',
        'nama_role',
    ];
    public $timestamps = false;
}
