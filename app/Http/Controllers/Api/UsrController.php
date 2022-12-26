<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsrController extends Controller
{
    public function index(Request $request): JsonResponse {
        return response()->json(
            Usr::all()->map( fn(Usr $usr) => $this->showUsr($usr) )
        );
    }

    private function showUsr(Usr $usr): array {
        return [
            'id' => $usr->id,
            'name' => $usr->name
        ];
    }
}
