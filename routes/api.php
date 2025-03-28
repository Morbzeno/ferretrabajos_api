<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AuthAdminController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\CartController;
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
Route::get('/admins/{id}', [AdminController::class, 'show']);
Route::post('/admins', [AdminController::class, 'store']); 
Route::delete('/admins/{id}', [AdminController::class, 'destroy']);
Route::post('/admins/{id}', [AdminController::class, 'update']); // Actualizar

Route::post('/register', [AuthAdminController::class, 'register']);
Route::post('/login', [AuthAdminController::class, 'login']);
Route::post('/logout', [AuthAdminController::class, 'logout']);



// Route::middleware(['auth.client'])->group(function () {
    Route::get('/clients', [ClientController::class, 'index']);
    Route::get('/clients/{id}', [ClientController::class, 'show']);
    Route::post('/clients', [ClientController::class, 'store']);
    Route::delete('/clients/{id}', [ClientController::class, 'destroy']);
    Route::post('/clients/{id}', [ClientController::class, 'update']);
// });

Route::get('/workers', [WorkerController::class, 'index']);
Route::get('/workers/{id}', [WorkerController::class, 'show']);
Route::post('/workers', [WorkerController::class, 'store']); 
Route::delete('/workers/{id}', [WorkerController::class, 'destroy']);
Route::post('/workers/{id}', [WorkerController::class, 'update']); // Actualizar

Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);
 
    return ['token' => $token->plainTextToken];
});

Route::prefix('cart')->group(function () {
    Route::get('/{client_id}', [CartController::class, 'show']);
    Route::post('/{client_id}/add', [CartController::class, 'addWorker']);
    Route::post('/{client_id}/remove', [CartController::class, 'removeWorker']);
    Route::post('/{client_id}/clear', [CartController::class, 'clearCart']);
});