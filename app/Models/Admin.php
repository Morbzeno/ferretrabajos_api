<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'admins';
    protected $fillable = ['nombre', 'edad', 'carrera', 'correo', 'materias', 'password'];
}
