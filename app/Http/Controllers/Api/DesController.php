<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Des;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DesController extends Controller
{
    public function index(Request $request): JsonResponse {
        return response()->json(
            Des::all()->map( fn(Des $des) => $this->showDes($des) )
        );
    }

    private function showDes(Des $des): array {
        return [
            'id' => $des->id,
            'name' => $des->name
        ];
    }
}
