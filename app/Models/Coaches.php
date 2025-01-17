<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coaches extends Model
{
    use HasFactory;

    protected $table = 'coaches';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'nama',
        'jenis_kelamin',
        'tanggal_lahir',
        'pengalaman_tahun',
        'lokasi_kerja',
        'email'
    ];
}
