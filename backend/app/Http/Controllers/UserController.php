<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all());
    }

    public function show($id)
    {
        $user = User::where('id', $id)->select(
            'id',
            'first_name',
            'last_name',
            'middle_name',
            'email',
            'role',
            'username',
            'created_at'
        )->first();
        if ($user) {
            return response()->json($user);
        } else {
            return response()->json(['message' => 'User not found.'], 404);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'username' => ['required', 'string', 'max:255', 'unique:' . User::class],
            'role' => ['required', 'integer', 'in:0,1'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'username' => $request->username,
            'role' => $request->role,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        return response()->json(['message' => 'Successfully created user', 'user' => $user]);
    }

    public function update(Request $request, $id)
    {
        if ($request->password || $request->password_confirmation) {
            $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'middle_name' => ['nullable', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class . ',email,' . $id],
                'role' => ['required', 'integer', 'in:0,1'],
                'username' => ['required', 'string', 'max:255', 'unique:' . User::class . ',username,' . $id],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
        } else {
            $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'middle_name' => ['nullable', 'string', 'max:255'],
                'role' => ['required', 'integer', 'in:0,1'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class . ',email,' . $id],
                'username' => ['required', 'string', 'max:255', 'unique:' . User::class . ',username,' . $id],
            ]);
        }

        $user = User::findOrFail($id);

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->middle_name = $request->middle_name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->role = $request->role;

        if ($request->password) {
            $user->password = $request->password;
        }
        $user->save();
        return response()->json(['message' => 'User has been updated successfully']);
    }

    public function destroy(Request $request, $id)
    {
        // Don't let the current user delete themselves!
        $currentUserId = $request->user()->id;
        if ($currentUserId === (int) $id) {
            return response()->json(['message' => "Please don't delete yourself"], 403);
        }

        $user = User::find($id);
        if ($user) {
            // Soft delete the user
            $user->delete();
            return response()->json(['message' => 'User deleted successfully.']);
        } else {
            return response()->json(['message' => 'User not found.'], 404);
        }
    }
}
