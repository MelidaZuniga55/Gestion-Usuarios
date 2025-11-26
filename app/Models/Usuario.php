<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;


class Usuario extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'password',
        'telefono',
        'fecha_nacimiento',
        'direccion',
        'activo'
    ];

    protected $hidden = [
    'password',
    'remember_token'
];


    protected $casts = [
        'fecha_nacimiento' => 'date',
        'activo' => 'boolean'
    ];
}