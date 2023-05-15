<?php

use App\Http\Controllers\LocalImagesController;
use App\Http\Controllers\LocalSessionController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\UserSessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// This route will handle a unique session for an unidentified user
Route::prefix('session')->group(function () {
    Route::get('/local',
        [LocalSessionController::class, 'create']
    ); // Since this is an unidentified route we do not need to authenticate

    Route::get('/confirmation/{link}',
        [RegistrationController::class, 'confirmation']
    ); // This is a link sent by the mailer, so, no authorization is handled

    Route::post('/resend/{link}',
        [RegistrationController::class, 'resend']
    ); // This is a link sent by the confirmation route, so, no authorization is handled
});

Route::middleware('livinAuth')->group(function () {
    Route::prefix('session')->group(function () {
        Route::post('/registration',
            [RegistrationController::class, 'registration']);

        Route::post('/signin',
            [UserSessionController::class, 'signin']);

        Route::post('/signout',
            [UserSessionController::class, 'signout']);

        Route::patch('/update',
            [UserSessionController::class, 'update']);

        Route::get('/names',
            [UserSessionController::class, 'names']);
    });

    Route::prefix('images')->group(function () {
        Route::get('/user',
            [LocalImagesController::class, 'userImage']);
        Route::patch('/user/update',
            [LocalImagesController::class, 'updateUserImage']);
    });

    Route::prefix('location')->group(function () {
        Route::get('/cp',
            [LocationController::class, 'zipCode']);
    });
});

