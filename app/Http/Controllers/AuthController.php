<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function create(Request $request)
    {
        $rules = [
            'email' => 'required|email|unique:users,email',
            'password' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['http' => Response::HTTP_INTERNAL_SERVER_ERROR, 'error' => $validator->messages()];
        }

        $email = $request->input('email');
        $password = $request->input('password');

        $newUser = new User();
        $newUser->email = $email;
        $newUser->password = password_hash($password, PASSWORD_DEFAULT);
        $newUser->token = '';
        $newUser->save();

        return ['http' => Response::HTTP_OK, 'success' => 'Usuário criado com sucesso!'];
    }

    public function login(Request $request)
    {
        $creds = $request->only('email', 'password');

        $token = Auth::attempt($creds);

        if (!$token) {
            return ['http' => Response::HTTP_UNAUTHORIZED, 'error' => 'E-mail ou senha incorretos'];
        }

        return ['token' => $token];
    }

    public function logout()
    {
        Auth::logout();

        return ['http' => Response::HTTP_OK, 'success' => 'Usuário deslogado com sucesso!'];
    }
}
