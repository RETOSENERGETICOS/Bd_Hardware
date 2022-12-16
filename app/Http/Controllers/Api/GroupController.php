<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index(Request $request): JsonResponse {
        return response()->json(
            Group::all()->map( fn(Group $group) => $this->showGroup($group) )
        );
    }

    private function showGroup(Group $group): array {
        return [
            'id' => $group->id,
            'name' => $group->name
        ];
    }
}
