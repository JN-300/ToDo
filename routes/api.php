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

        Route::get('tasks/overdue', \App\Http\Controllers\Api\Task\Overdue::class);
        Route::get('tasks/project/{project}', \App\Http\Controllers\Api\Task\ByProject::class);
        Route::get('tasks/user/{user}', \App\Http\Controllers\Api\Task\ByUser::class);

        Route::apiResource('tasks', \App\Http\Controllers\Api\TaskController::class);
        Route::apiResource('projects', \App\Http\Controllers\Api\ProjectController::class);
        Route::get('/user', function (Request $request) {
            return response()->json($request->user());
        });

        Route::name('admin.')
            ->middleware('isAdmin')
            ->prefix('admin')
            ->group(function () {
                Route::apiResource('tasks', \App\Http\Controllers\Api\Admin\TaskController::class)
                    ->only('index', 'show', 'update', 'destroy')
                ;
            });
    });

//
//Route::get('/user', function (Request $request) {
//    return response()->json($request->user());
//})->middleware('auth:sanctum');
