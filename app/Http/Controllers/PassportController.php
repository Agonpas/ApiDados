<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use League\OAuth2\Server\Exception\OAuthServerException;

//use Laravel\Passport\Passport;


class PassportController extends Controller
{
    public function login(Request $request) 
    {
        
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $success['token'] = $user->createToken('API Token')->accessToken;
            $success['id'] = $user->id;
            $success['name'] = $user->name;
            $success['email'] = $user->email;
            
            return response()->json([$success, 
                'message' => 'Usuario logeado con éxito.'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Error de inicio de sesión. Error en mail o contraseña.'
            ], 401);

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
           
           return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        $user = User::create($input);
        $user->assignRole('player');

        /*$success['token'] = $user->createToken('API Token')->accessToken;
        $success['id'] = $user->id;
        $success['name'] = $user->name;
        $success['role'] = 'player';*/
        
       
        return response()->json([
            'token' => $user->createToken('API Token')->accessToken,
            'id' => $user->id,
            'name' => $user->name,
            'role' => 'player',
            'message' => 'Usuario registrado con éxito.'
        ], 201);
    }
   
    public function logout()
    {
        $user = Auth::user();

        if ($user) {

            $user->tokens->each->revoke();

            return response()->json('Gracias por usar nuestro servicio', 200);
        } else {
            return response()->json('No tienes sesión iniciada', 401);
        }
    }
    
}
