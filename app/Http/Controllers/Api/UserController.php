<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse {
        $users = User::with('role')->get()->map(static function(User $user) {
            return [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role->name,
                'created_at' => $user->created_at
            ];
        });
        return response()->json($users);
    }

    public function store(Request $request) {
        $request->validateWithBag('user', [
            'email' => 'email|unique:users'
        ]);
        \DB::beginTransaction();
        try {
            $user = User::create([
                'email' => $request->email,
                'password' => \Hash::make($request->password),
                'role_id' => $request->role['id']
            ]);
            $user->save();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
        \DB::commit();
        return response()->json(
            $this->showUser($user)
        );
    }

    public function show(Request $request, User $user): JsonResponse {
        return response()->json(
            $this->showUser(User::with('role')->find($user->id))
        );
    }

    public function update(Request $request, User $user): JsonResponse {
        \DB::beginTransaction();
        try {
            $user->update([
                //'email' => $request->email,
                'role_id' => $request->role['id']
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
        \DB::commit();
        return response()->json(
            $this->showUser($user->refresh())
        );
    }

    public function destroy(Request $request, User $user) {
        \DB::beginTransaction();
        try {
            $user->delete();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
        \DB::commit();
        return response()->json('Usuario eliminado correctamente');
    }

    private function showUser(User $user) {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at
        ];
    }
}
