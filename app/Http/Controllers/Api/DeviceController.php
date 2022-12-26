<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request): JsonResponse {
        return response()->json(
            Device::all()->map( fn(Device $device) => $this->showDevice($device) )
        );
    }

    private function showDevice(Device $device): array {
        return [
            'id' => $device->id,
            'name' => $device->name
        ];
    }
}
