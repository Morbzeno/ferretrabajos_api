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
        // dd($request->all());
        // Validar los datos
        $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|unique:workers',
            'password' => 'required|string|min:8|max:255',
            'image' => 'nullable',
            'rfc' => 'required|string|max:255',
            'description' => 'required|string',
            'specialty' => 'required|string|max:255'
        ]);
    
        // Crear una nueva instancia de Worker
        $worker = new Worker();
        $worker->firstName = $request->firstName;
        $worker->lastName = $request->lastName;
        $worker->email = $request->email;
        $worker->password = bcrypt($request->password);
        $worker->rfc = $request->rfc;
        $worker->description = $request->description;
        $worker->specialty = $request->specialty;
    
        // Verificar si hay una imagen en la solicitud
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $ruta = $image->store('admins', 'public'); // Almacena en storage/app/public/admins
            $worker->image = $ruta;
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
    public function update(Request $request, $id) {
        $worker = Worker::find($id);
    
        if (!$worker) {
            return response()->json(['message' => 'Worker no encontrado'], 404);
        }
    
        $request->validate([
            'firstName' => 'sometimes|string|max:255',
            'lastName' => 'sometimes|string|max:255',
            'age' => 'sometimes|integer',
            'email' => "sometimes|email",
            'password' => 'sometimes|string|min:6|max:255',
            'image' => 'nullable',
            'RFC' => 'sometimes|string|max:255',
            'specialty' => 'sometimes|string|max:255'
        ]);
    
        // Actualizar los datos excepto la imagen y la contraseña
        $worker->update($request->except(['password', 'image']));
    
        // Si se envía una nueva contraseña, la encripta y guarda
        if ($request->filled('password')) {
            $worker->password = Hash::make($request->password);
        }
    
        // Si se envía una nueva imagen, la almacena y actualiza la BD
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $nombreImagen = time() . '_' . $image->getClientOriginalName();
            $ruta = $image->storeAs('clients', $nombreImagen, 'public');
            $worker->image = $ruta;
        }
    
        // Guardar los cambios en la base de datos
        $worker->save();
    
        return response()->json([
            'message' => 'Worker actualizado correctamente',
            'data' => $worker
        ], 200);
    }
    
}
