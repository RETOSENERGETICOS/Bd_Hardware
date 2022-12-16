<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(Request $request): JsonResponse {
        return response()->json(
            Brand::all()->map( fn(Brand $brand) => $this->showBrand($brand) )
        );
    }

    private function showBrand(Brand $brand): array {
        return [
            'id' => $brand->id,
            'name' => $brand->name
        ];
    }
}
