<?php

namespace App\Http\Controllers;
namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Registro de cliente
    public function register(Request $request)
    {
        
        // Validar los datos, incluyendo la image
        $request->validate([
            'name' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|unique:admins',
            'password' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'socialMedia' => 'required|string|max:255',
            'phone' => 'required|integer',
            'status' => 'required|string|max:255',
            'address' => 'required|string|max:255'
             // Validación de image
        ]);
    
        // Crear una nueva instancia de Client
        $client = new Client();
        $client->name = $request->name;
        $client->lastName = $request->lastName;
        $client->email = $request->email;
        $client->password = Hash::make($request->password);
        $client->socialMedia = $request->socialMedia;
        $client->phone = $request->phone;
        $client->status = $request->status;
        $client->address = $request->address;
    
        // Verificar si hay una image en la solicitud
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $nombreImagen = time() . '_' . $image->getClientOriginalName();
            $ruta = $image->storeAs('admins', $nombreImagen, 'public'); // Guardar en storage/app/public/admins
            $client->image = $ruta; // Guardar la ruta en la base de datos
        }
    
        // Guardar en la base de datos
        $client->save();
    
        return response()->json([
            'message' => 'client insertado correctamente',
            'data' => $client
        ], 201);
    }

    // Inicio de sesión

public function login(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    $client = Client::where('email', $request->email)->first();

    if (!$client || !Hash::check($request->password, $client->password)) {
        return response()->json(['message' => 'Credenciales inválidas'], 401);
    }

    // Generar un token único
    $token = Str::random(60);

    // Guardar el token en el cliente (MongoDB)
    $client->token = hash('sha256', $token);
    $client->save();

    return response()->json([
        'message' => 'Login correcto',
        'token'   => $token,
        'client'  => $client
    ]);
}



    // Cierre de sesión
    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json(['message' => 'Cierre de sesión exitoso']);
    }
}
