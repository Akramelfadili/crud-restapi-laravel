<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request){
        $useFields = $request->validate([
            "name" => "required|string",
            "email" => "required|string|unique:users,email",
            "password" => "required|string|confirmed"
        ]);

        $user = User::create([
            "name" => $useFields["name"],
            "email" =>$useFields["email"],
            "password" => bcrypt($useFields["password"])
        ]);

        $token = $user->createToken("myapptoken")->plainTextToken;
        $response = [
            "user" =>$user,
            "token" => $token
        ];

        return response($response,201);
    }

    public function login(Request $request){
        $useFields = $request->validate([
            "email" => "required|string",
            "password" => "required|string"
        ]);

        $user = User::where("email",$useFields["email"])->first();
        if (!$user || !Hash::check($useFields["password"] , $user->password)){
            return response([
                "message" => "Wrong",
            ],401);
        }

        $token = $user->createToken("myapptoken")->plainTextToken;
        $response = [
            "user" =>$user,
            "token" => $token
        ];

        return response($response,201);
    }

    public function logout(Request $request){
        auth()->user()->tokens()->delete(); 
        return [
            "message" => "Token Destroyed / Loged Out"
        ];
    }
}
