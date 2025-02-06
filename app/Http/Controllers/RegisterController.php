<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|max:255',
            'email' => 'email|unique:admins',
            'password' => 'required|confirmed',
        ]);
        $admin = Admin::create($fields);
        $token = $admin->createToken($request->name);
        return [
            "admin" => $admin,
            "token" => $token->plainTextToken
        ];
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'email|required',
            'password' => 'required',
        ]);
        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return [
                'message' => 'The provided credentials are incorrect'
            ];
        }
        $token = $admin->createToken($admin->password);
        return [
            "admin" => $admin,
            "token" => $token->plainTextToken
        ];
    }
}
