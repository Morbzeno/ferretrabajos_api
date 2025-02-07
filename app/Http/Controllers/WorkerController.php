<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Worker;
use Illuminate\Support\Facades\Hash;
class WorkerController extends Controller
{
    public function index()
    {
        return response()->json(Worker::all());
    }

    // Obtener un worker por su ID
    public function show($id)
    {
        $worker = Worker::find($id);
        return $worker ? response()->json($worker) : response()->json(['error' => 'Worker no encontrado'], 404);
    }

    // Crear un nuevo worker
    public function store(Request $request)
{
    // Validar los datos, incluyendo la image
    $request->validate([
        'name' => 'required|string|max:255',
        'lastName' => 'required|string|max:255',
        'age' => 'required|integer',
        'email' => 'required|email|unique:admins',
        'password' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'RFC' => 'required|string|max:255',
        'Especiality' => 'required|string|max:255' // Validación de image
    ]);

    // Crear una nueva instancia de Worker
    $worker = new Worker();
    $worker->name = $request->name;
    $worker->lastName = $request->lastName;
    $worker->age = $request->age;
    $worker->email = $request->email;
    $worker->password = Hash::make($request->password);
    $worker->RFC = $request->RFC;
    $worker->Especiality = $request->Especiality;

    // Verificar si hay una image en la solicitud
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $nombreImagen = time() . '_' . $image->getClientOriginalName();
        $ruta = $image->storeAs('admins', $nombreImagen, 'public'); // Guardar en storage/app/public/admins
        $worker->image = $ruta; // Guardar la ruta en la base de datos
    }

    // Guardar en la base de datos
    $worker->save();

    return response()->json([
        'message' => 'Worker insertado correctamente',
        'data' => $worker
    ], 201);
}
    public function destroy($id){
        $worker = Worker::find($id);
        $worker->delete();
        return response()->json(['message' => 'Worker eliminado correctamente'], 200);
    }
    public function update(Request $request, $id){
        $worker = Worker::find($id);
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'lastName' => 'sometimes|string|max:255',
            'age' => 'sometimes|integer',
            'email' => 'sometimes|email|unique:admins',
            'password' => 'sometimes|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'RFC' => 'sometimes|string|max:255',
            'Especiality' => 'sometimes|string|max:255' // Validación de image
        ]);
    $worker->update($request->only(['name', 'lastName', 'age', 'email', 'password', 'image', 'RFC', 'Especiality']));
    if ($request->filled('password')) {
        $worker->password = Hash::make($request->password);
    }

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $nombreImagen = time() . '_' . $image->getClientOriginalName();
        $ruta = $image->storeAs('clients', $nombreImagen, 'public');
        $worker->image = $ruta;
    }

    return response()->json([
        'message' => 'Worker actualizado correctamente',
        'data' => $worker
    ], 200);  
}
}
