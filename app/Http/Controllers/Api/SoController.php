<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\So;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SoController extends Controller
{
    public function index(Request $request): JsonResponse {
        return response()->json(
            So::all()->map( fn(So $so) => $this->showSo($so) )
        );
    }

    private function showSo(So $so): array {
        return [
            'id' => $so->id,
            'name' => $so->name
        ];
    }
}
