<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


class AdminController extends Controller
{
    public function index()
    {
        return response()->json(Admin::all());
    }

    // Obtener un admin por su ID
    public function show($id)
    {
        $admin = Admin::find($id);
        return $admin ? response()->json($admin) : response()->json(['error' => 'Admin no encontrado'], 404);
    }

    // Crear un nuevo admin
    public function store(Request $request)
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
        $img = $request->file('image');
        $nuevoNombre = 'admin' . $admin->id . '.' . $img->extension();
        $ruta = $img->storeAs('images/admins', $nuevoNombre, 'public');
        $rutaCompleta = asset('storage/' . $ruta);

        $admin->image = $rutaCompleta;
        $admin->save();
    }

    // Guardar en la base de datos
    $admin->save();

    return response()->json([
        'message' => 'Admin insertado correctamente',
        'data' => $admin
    ], 201);
}

    public function destroy($id){
        $admin = Admin::find($id);
        $admin->delete();
        return response()->json(['message' => 'Admin eliminado correctamente'], 200);
    }
    public function update(Request $request, $id){
        $admin = Admin::find($id);
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'lastName' => 'sometimes|string|max:255',
            'password' => 'sometimes|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'socialMedia' => 'sometimes|string|max:255',
            'phone' => 'sometimes|integer',
            'status' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255'
             // Validación de image
        ]);
    $admin->update($request->only(['name', 'lastName', 'password', 'image', 'socialMedia', 'phone', 'status', 'address']));
    if ($request->filled('password')) {
        $admin->password = Hash::make($request->password);
    }
    if ($request->hasFile('image')) {
        $img = $request->file('image');
        $nuevoNombre = 'admin' . $admin->id . '.' . $img->extension();
        $ruta = $img->storeAs('images/admins', $nuevoNombre, 'public');
        $rutaCompleta = asset('storage/' . $ruta);

        $admin->image = $rutaCompleta;
        $admin->save();
    }
    
    $admin->save();
    return response()->json([
        'message' => 'Admin actualizado correctamente',
        'data' => $admin
    ], 200);  
}
    
}
