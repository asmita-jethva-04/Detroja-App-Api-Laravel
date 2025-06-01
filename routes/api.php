<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\RelationsController;
use App\Http\Controllers\VillageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });

// Public Routes
Route::post('/register', [ApiController::class, 'register']);
Route::post('/login', [ApiController::class, 'login']);
Route::get('/guest_api', [ApiController::class, 'guest']);


// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiController::class, 'logout']);

    // ====================== user api ===============================
    Route::get('/user/index', [ApiController::class, 'index']);
    Route::post('/user/show', [ApiController::class, 'show']);
    Route::post('/user/update',[ApiController::class,'update']);
    Route::post('/user/delete',[ApiController::class,'delete']);


    // =================== village api ===============================

    Route::get('/village/index',[VillageController::class,'index']);
    Route::post('/village/store',[VillageController::class,'store']);
    Route::post('/village/show', [VillageController::class, 'show']);
    Route::post('/village/update',[VillageController::class,'update']);
    Route::post('/village/delete',[VillageController::class,'delete']);
    Route::post('/village/search',[VillageController::class,'search']);



    // =================== category api ===============================

    Route::get('/category/index',[CategoryController::class,'index']);
    Route::post('/category/store',[CategoryController::class,'store']);
    Route::post('/category/show',[CategoryController::class,'show']);
    Route::post('/category/update',[CategoryController::class,'update']);
    Route::post('/category/delete',[CategoryController::class,'delete']);

    // =================== directory api ===============================

    Route::get('/directory/index',[DirectoryController::class,'index']);
    Route::post('/directory/store',[DirectoryController::class,'store']);
    Route::post('/directory/show',[DirectoryController::class,'show']);
    Route::post('/directory/update',[DirectoryController::class,'update']);
    Route::post('/directory/delete',[DirectoryController::class,'delete']);
    Route::post('/directory/childid',[DirectoryController::class,'childid']);
    Route::post('/directory/childiddelete',[DirectoryController::class,'delete_childid']);


    // =================== relations api ===============================

    Route::get('/relations/index',[RelationsController::class,'index']);
    Route::post('/relations/store',[RelationsController::class,'store']);
    Route::post('/relations/show',[RelationsController::class,'show']);
    Route::post('/relations/update',[RelationsController::class,'update']);
    Route::post('/relations/delete',[RelationsController::class,'delete']);

    // =================== history api ===============================

    Route::get('/history/index',[HistoryController::class,'index']);
    Route::post('/history/delete',[HistoryController::class,'delete']);

    // =================== Search api ===============================
    
    Route::post('/search',[ApiController::class,'search']);


});

    Route::fallback(function () {
        return response()->json([
            'success' => false,
            'message' => 'Invalid API URL. Please check the endpoint.'
        ], 404);
    });


   