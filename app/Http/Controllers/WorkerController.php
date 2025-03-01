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
            $nombreImagen = time() . '_' . $image->extension();
            $ruta = $image->storeAs('images/worker', $nombreImagen, 'public'); // Guardar en storage/app/public/admins
            $rutaCompleta = asset('storage/' . $ruta);
            $worker->image = $rutaCompleta; // Guardar la ruta en la base de datos
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
        $worker = Worker::findOrFail($id);
    
        $request->validate([
            'name' => 'string|max:255',
            'lastName' => 'string|max:255',
            'age' => 'integer',
            'email' => 'email',
            'password' => 'string|min:6|max:255',
            'image' => '',
            'rfc' => 'string|max:255',
            'specialty' => 'string|max:255'
        ]);
    
        // Actualizar datos excepto la imagen y la contraseña
        $worker->update($request->except(['password', 'image']));
    
        // Si se envía una nueva contraseña, la encripta y guarda
        if ($request->filled('password')) {
            $worker->password = Hash::make($request->password);
        }
    
        // Si se envía una nueva imagen, elimina la anterior y guarda la nueva
        if ($request->hasFile('image')) {
            $img = $request->file('image');
            $nuevoNombre = 'worker' . $worker->id . '.' . $img->extension();
            $ruta = $img->storeAs('images/workers', $nuevoNombre, 'public');
            $rutaCompleta = asset('storage/' . $ruta);
    
            $worker->image = $rutaCompleta;
            $worker->save();
        }
    
        // Guardar cambios
        $worker->save();
    
        return response()->json([
            'message' => 'Worker actualizado correctamente',
            'data' => $worker
        ], 200);
    }
    
    
}
