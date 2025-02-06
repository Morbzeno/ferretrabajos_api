<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\WorkerController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('/ping', function (Request $request) {    
    $connection = DB::connection('mongodb');
    $msg = 'MongoDB is accessible!';
    
    try {  
        $connection->command(['ping' => 1]);  
    } catch (\Exception $e) {  
        $msg = 'MongoDB is not accessible. Error: ' . $e->getMessage();
    }

    return response()->json(['msg' => $msg]);
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/admins', [AdminController::class, 'index']);
Route::post('/admins', [AdminController::class, 'store']); 
Route::delete('/admins/{id}', [AdminController::class, 'destroy']);
Route::put('/admins/{id}', [AdminController::class, 'update']); // Actualizar

Route::post('/login', [RegisterController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/clients', [ClientController::class, 'index']);
Route::post('/clients', [ClientController::class, 'store']); 
Route::delete('/clients/{id}', [ClientController::class, 'destroy']);
Route::put('/clients/{id}', [ClientController::class, 'update']); // Actualizar

Route::get('/workers', [WorkerController::class, 'index']);
Route::post('/workers', [WorkerController::class, 'store']); 
Route::delete('/workers/{id}', [WorkerController::class, 'destroy']);
Route::put('/workers/{id}', [WorkerController::class, 'update']); // Actualizar