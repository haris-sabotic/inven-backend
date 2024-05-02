<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();

        if ($request->has('name')) {
            $user->name = $request->input('name');
        }

        if ($request->has('email')) {
            $user->email = $request->input('email');
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        if ($request->has('phone')) {
            $user->phone = $request->input('phone');
        }

        if ($request->has('about')) {
            $user->about = $request->input('about');
        }

        if ($request->hasFile('photo')) {
            $path = $request->photo->store('images', 'public');

            $user->photo = $path;
        }

        if ($request->hasFile('cv')) {
            $path = $request->cv->store('documents', 'public');

            $user->cv = $path;
        }

        $user->save();

        return [
            'message' => 'Success.',
            'new_user' => $user
        ];
    }
}
