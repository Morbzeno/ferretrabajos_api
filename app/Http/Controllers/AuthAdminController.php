<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthAdminController extends Controller
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
            'status' => '',
            'address' => 'required|string|max:255'
             // Validación de image
        ]);
    
        // Crear una nueva instancia de Admin
        $admin = new Admin();
        $admin->name = $request->name;
        $admin->lastName = $request->lastName;
        $admin->email = $request->email;
        $admin->password = Hash::make($request->password);
        $admin->socialMedia = $request->socialMedia;
        $admin->phone = $request->phone;
        $admin->status = $request->status;
        $admin->address = $request->address;
    
        // Verificar si hay una image en la solicitud
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $nombreImagen = time() . '_' . $image->getClientOriginalName();
            $ruta = $image->storeAs('admins', $nombreImagen, 'public'); // Guardar en storage/app/public/admins
            $admin->image = $ruta; // Guardar la ruta en la base de datos
        }
    
        // Guardar en la base de datos
        $admin->save();
    
        return response()->json([
            'message' => 'admin insertado correctamente',
            'data' => $admin
        ], 201);
    }

    // Inicio de sesión

public function login(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    $admin = Admin::where('email', $request->email)->first();

    if (!$admin || !Hash::check($request->password, $admin->password)) {
        return response()->json(['message' => 'Credenciales inválidas'], 401);
    }

    // Generar un token único
    $token = Str::random(60);

    // Guardar el token en el cliente (MongoDB)
    $admin->token = hash('sha256', $token);
    $admin->save();

    return response()->json([
        'message' => 'Login correcto',
        'token'   => $token,
        'admin'  => $admin
    ]);
}



    // Cierre de sesión
    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json(['message' => 'Cierre de sesión exitoso']);
    }
}
