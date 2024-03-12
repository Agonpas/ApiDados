<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Spatie\Permission\Models\Role;

//use Laravel\Passport\Passport;


class PassportController extends Controller
{
    public function login(Request $request) 
    {
        try {
            $user = User::where('email', $request->email)->first();
    
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['error' => 'Error de inicio de sesión. Error en mail o contraseña.'], 401);
            }
    
            $token = $user->createToken('example')->accessToken;
    
            return response()->json([
                'message' => 'Has iniciado sesión',
                'user' => $user,
                'token' => $token
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        

    }
    public function register(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'nickname' => 'nullable|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|alpha_num'
           
        ]);
    
        if ($validator->fails()) {
           
           return response()->json(['Error de validación', $validator->errors()], 401);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('API Token')->accessToken;
        $success['name'] = $user->name;
        $user->assignRole('player');
    
       
        return response()->json([$success, 'Usuario registrado con éxito.'], 201);
    }


    
}
