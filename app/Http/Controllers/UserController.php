<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('permissions')
            ->latest()
            ->paginate(50);

        $permissions = Permission::query()
            ->oldest()
            ->get()
            ->pluck('name');

        if ($request->wantsJson())
            return UserResource::collection($users);

        return Inertia::render('Users', [
            'permissions' => $permissions,
            'users' => UserResource::collection($users),
        ]);
    }

    public function store(UserRequest $request)
    {
        $user = User::create([
            'name' => $request->validated()['name'],
            'email' => $request->validated()['email'],
            'password' => Hash::make($request->validated()['password']),
        ]);

        $permissions = Permission::whereIn('name', $request->validated()['permissions'])->get();
        $user->syncPermissions($permissions);

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => __('User created successfully'),]);
    }

    public function update(UserRequest $request, User $user)
    {
        DB::transaction(function () use ($request, $user) {
            $user->update([
                'name' => $request->validated()['name'],
                'email' => $request->validated()['email'],
            ]);

            if ($request->validated()['password'])
                $user->update([
                    'password' => Hash::make($request->validated()['password']),
                ]);

            if (User::count() === 1 && (!in_array('show users', $request->validated()['permissions']) || !in_array('edit users', $request->validated()['permissions'])))
                throw ValidationException::withMessages(['permissions' => __('You cannot remove the only user\'s permissions')]);

            $permissions = Permission::whereIn('name', $request->validated()['permissions'])->get();
            $user->syncPermissions($permissions);
        });

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => __('User updated successfully'),]);
    }

    public function destroy(User $user)
    {
        if (User::count() === 1)
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => __('You cannot delete the only user'),]);

        $user->delete();

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => __('User deleted successfully'),]);
    }
}
