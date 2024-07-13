<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::name('token.' )
    ->prefix('token')
    ->group(function () {
        Route::post('/', [\App\Http\Controllers\Api\TokenController::class, 'createToken'])
            ->name('create');

        Route::delete('/', [\App\Http\Controllers\Api\TokenController::class, 'deleteToken'])
            ->name('delete')
            ->middleware('auth:sanctum')
        ;
    });


Route::middleware('auth:sanctum')
    ->group(function () {
        Route::apiResource('tasks', \App\Http\Controllers\Api\TaskController::class);

        Route::get('projects/{project}/tasks', \App\Http\Controllers\Api\Project\ProjectTasks::class);
        Route::apiResource('projects', \App\Http\Controllers\Api\ProjectController::class);
        Route::get('/user', function (Request $request) {
            return response()->json($request->user());
        });
    });

//
//Route::get('/user', function (Request $request) {
//    return response()->json($request->user());
//})->middleware('auth:sanctum');
