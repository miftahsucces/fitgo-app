<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clients extends Model
{
    use HasFactory;

    protected $table = 'client';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id_user',
        'email2',
        'jenis_kelamin',
        'tanggal_lahir',
        'tinggi_badan',
        'berat_badan',
        'golongan_darah',
        'alamat',
        'telepon',
        'about_me',
        'profile_foto',
        'aktifitas',
        'tujuan',
        'medis',
    ];
}
