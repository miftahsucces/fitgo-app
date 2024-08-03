<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Body extends Model
{
    use HasFactory;
    protected $table = 'client_body_composition';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id_client',
        'result_day',
        'weigth',
        'body_fat',
        'body_water',
        'muscle_mass',
        'physical_rating',
        'bmr',
        'metabolic_age',
        'bone_mass',
        'visceral_fat',
        'date_actual',
    ];
}
