<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController,
    RoleController,
    UserController,
    ToolController,
    DesController,
    GroupController,
    FamilyController,
    DeviceController,
    UsrController,
    SoController,
    BrandController,
    UploadController
};

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(static function() {
    Route::get('/user/verify', [AuthController::class, 'verify']);
    Route::get('/roles', [RoleController::class, 'index']);
    Route::delete('/uploads', [UploadController::class, 'destroy']);
    Route::post('/search', [ToolController::class, 'search']);
    Route::get('/history', [ToolController::class, 'history']);
    Route::delete('/logout', [AuthController::class, 'logout']);
    Route::apiResources([
        'users' => UserController::class,
        'tools' => ToolController::class,
        'dess' => DesController::class,
        'groups' => GroupController::class,
        'sos' => SoController::class,
        'usrs' => UsrController::class,
        'devices' => DeviceController::class,
        'uploads' => UploadController::class,
    ]);
});
