<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Client;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Obtener el carrito del cliente
    public function show($client_id)
{
    $cart = Cart::where('client_id', $client_id)->first();

    // Verifica si el carrito existe
    if (!$cart) {
        return response()->json(['message' => 'Cart not found'], 404);
    }

    // Si `workers` es una cadena JSON, conviértelo manualmente
    if (is_string($cart->workers)) {
        $cart->workers = json_decode($cart->workers, true);
    }

    return response()->json($cart);
}

    // Agregar un trabajador al carrito
    public function addWorker(Request $request, $client_id)
{
    // dd($request->all()); // Esto te mostrará todos los datos recibidos
    $cart = Cart::firstOrCreate(
        ['client_id' => $client_id],
        ['workers' => []]
    );

    // Asegurar que workers es un array
    if (!is_array($cart->workers)) {
        $cart->workers = [];
    }

    // Obtener worker_id desde el input
    $worker_ids = $request->input('worker_id'); // Puede ser un string o un array

    // Si worker_id es un solo valor (string), convertirlo en array
    if (!is_array($worker_ids)) {
        $worker_ids = [$worker_ids]; // Convertir a array si es un string
    }

    // Agregar cada worker_id al carrito sin duplicados
    foreach ($worker_ids as $worker_id) {
        if (!empty($worker_id) && !in_array($worker_id, $cart->workers)) {
            $cart->push('workers', $worker_id, true); // Agrega sin duplicados
        }
    }

    $cart->save();

    return response()->json($cart);
}

    

    // Eliminar un trabajador del carrito
    public function removeWorker(Request $request, $client_id)
    {
        $cart = Cart::where('client_id', $client_id)->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $worker_id = $request->input('worker_id');

        $cart->workers = array_values(array_diff($cart->workers, [$worker_id]));
        $cart->save();

        return response()->json($cart);
    }

    // Vaciar el carrito
    public function clearCart($client_id)
    {
        $cart = Cart::where('client_id', $client_id)->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $cart->workers = [];
        $cart->save();

        return response()->json(['message' => 'Cart cleared']);
    }
}