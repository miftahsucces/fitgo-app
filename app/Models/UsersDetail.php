<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersDetail extends Model
{
    protected $table = 'users_detail';

    protected $fillable = [
        'id_user',
        'id_member',
        'tipe_anggota',
        'jenis_kelamin',
        'tanggal_lahir',
        'tinggi_badan',
        'berat_badan',
        'golongan_darah',
        'alamat',
        'telepon',
        'about_me',
        'profile_foto'
    ];

    // Define the relationship with User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
