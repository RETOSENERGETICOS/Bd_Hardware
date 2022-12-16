<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Family;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FamilyController extends Controller
{
    public function index(Request $request): JsonResponse {
        return response()->json(
            Family::all()->map( fn(Family $family) => $this->showFamily($family) )
        );
    }

    private function showFamily(Family $family): array {
        return [
            'id' => $family->id,
            'name' => $family->name
        ];
    }
}
