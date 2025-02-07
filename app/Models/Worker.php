<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'workers';
    protected $fillable = ['name', 'lastName', 'age', 'email', 'password', 'image', 'RFC', 'Especiality'];
}
