<?php
namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class Cart extends Model
{
    use SoftDeletes;

    protected $collection = 'carts'; // Nombre de la colección en MongoDB
    protected $fillable = ['client_id', 'workers'];

    protected $casts = [
        // 'workers' => 'array', // Almacenar los IDs de los trabajadores en un array
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}