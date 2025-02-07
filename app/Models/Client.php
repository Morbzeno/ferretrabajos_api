<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Authenticatable;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class Client extends Model implements AuthenticatableContract
{
    use HasApiTokens, Notifiable, Authenticatable;

    protected $connection = 'mongodb';
    protected $collection = 'Clientes';

    protected $fillable = ['name', 'lastName', 'email', 'password', 'image', 'socialMedia', 'phone', 'status', 'address'];

    protected $hidden = ['password'];
    public function createToken($name, array $abilities = ['*'])
{
    return new PersonalAccessToken(['token' => hash('sha256', $name)]);
}
}
