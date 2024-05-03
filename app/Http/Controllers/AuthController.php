<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:individual,company',
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required',
        ]);

        $user = new User();
        $user->type = $validated['type'];
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->photo = ($validated['type'] == 'individual') ? 'images/default_individual_photo.png' : 'images/default_company_photo.png';
        $user->save();

        $token = $user->createToken('authToken')->plainTextToken;
        return ['token' => $token, 'user' => User::find($user->id)];
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('email', $validated['email'])->first();
        if (!$user) {
            return response()->json([
                'message' => 'No user with that email exists.',
                'errors' => [
                    'email' => ['No user with that email exists.']
                ]
            ], 401);
        }

        if (Hash::check($validated['password'], $user->password)) {
            $token = $user->createToken('authToken')->plainTextToken;
            return ['token' => $token, 'user' => User::find($user->id)];
        } else {
            return response()->json([
                'message' => 'Incorrect password.',
                'errors' => [
                    'password' => ['Incorrect password.']
                ]
            ], 401);
        }
    }
}
