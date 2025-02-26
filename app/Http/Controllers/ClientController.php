<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function index()
    {
        return response()->json(Client::all());
    }

    // Obtener un Client por su ID
    public function show($id)
    {
        $Client = Client::find($id);
        return $Client ? response()->json($Client) : response()->json(['error' => 'Client no encontrado'], 404);
    }

    // Crear un nuevo Client
    public function store(Request $request)
    {
       // Validar los datos, incluyendo la image
        $request->validate([
            'name' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|unique:admins',
            'password' => 'required|string|max:255',
            'image' => 'nullable|max:2048',
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
            $nombreImagen = time() . '_' . $image->extension();
            $ruta = $image->storeAs('images/clients', $nombreImagen, 'public'); // Guardar en storage/app/public/admins
            $rutaCompleta = asset('storage/' . $ruta);
            $client->image = $rutaCompleta; // Guardar la ruta en la base de datos
        }
    
        // Guardar en la base de datos
        $client->save();
    
        return response()->json([
            'message' => 'client insertado correctamente',
            'data' => $client
        ], 201);
    }
    public function destroy($id){
        $client = Client::findorfail($id);
        $client->delete();
        return response()->json(['message' => 'client eliminado correctamente'], 200);
    }
    public function update(Request $request, $id){
        $client = Client::find($id);
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'lastName' => 'sometimes|string|max:255',
            'password' => 'sometimes|string|max:255',
            'image' => 'sometimes',
            'socialMedia' => 'sometimes|string|max:255',
            'phone' => 'sometimes|integer',
            'status' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255'
             // Validación de image
        ]);
    $client->update($request->only(['name', 'lastName', 'email', 'password', 'image', 'socialMedia', 'phone', 'status', 'address']));
    if ($request->filled('password')) {
        $client->password = Hash::make($request->password);
    }

    if ($request->hasFile('imagen')) {
        $imagen = $request->file('imagen');
        $nombreImagen = time() . '_' . $imagen->getClientOriginalName();
        $ruta = $imagen->storeAs('clients', $nombreImagen, 'public');
        $client->imagen = $ruta;
    }


    
    return response()->json([
        'message' => 'client actualizado correctamente',
        'data' => $client
    ], 200);  
}
    
}
