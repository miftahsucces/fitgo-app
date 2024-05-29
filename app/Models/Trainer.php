<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    protected $table = 'trainer';

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
        'profile_foto'
    ];

    // Define the relationship with User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
