<?php

use App\Http\Controllers\EstateController;
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

        Route::get('refresh',
            [UserSessionController::class, 'refresh']);

        Route::post('refresh',
            [UserSessionController::class, 'restore']);
    });

    Route::prefix('images')->group(function () {
        Route::get('/user',
            [LocalImagesController::class, 'userImage']);
        Route::patch('/user/update',
            [LocalImagesController::class, 'updateUserImage']);
    });

    Route::prefix('estate')->group(function () {
        // Amenities
        Route::get('/amenity',
            [EstateController::class, 'amenities']);
        Route::put('/amenity',
            [EstateController::class, 'createAmenity']);
        Route::patch('/amenity',
            [EstateController::class, 'updateAmenity']);
        Route::delete('/amenity',
            [EstateController::class, 'removeAmenity']);

        // Status
        Route::get('/status',
            [EstateController::class, 'status']);
        Route::put('/status',
            [EstateController::class, 'createStatus']);
        Route::patch('/status',
            [EstateController::class, 'updateStatus']);
        Route::delete('/status',
            [EstateController::class, 'removeStatus']);

        // Types
        Route::get('/types',
            [EstateController::class, 'types']);
        Route::put('/types',
            [EstateController::class, 'createType']);
        Route::patch('/types',
            [EstateController::class, 'updateType']);
        Route::delete('/types',
            [EstateController::class, 'removeType']);

        // Uses
        Route::get('/uses',
            [EstateController::class, 'uses']);
        Route::put('/uses',
            [EstateController::class, 'createUse']);
        Route::patch('/uses',
            [EstateController::class, 'updateUse']);
        Route::delete('/uses',
            [EstateController::class, 'removeUse']);

        // Route properties
        Route::put('/new',
            [EstateController::class, 'newProperty']);
        Route::patch('/update/{id}',
            [EstateController::class, 'updateProperty']);
        Route::delete('/remove/{id}',
            [EstateController::class, 'removeProperty']);
        Route::get('/information/{id}',
            [EstateController::class, 'property']);
        Route::get('/information/user/all',
            [EstateController::class, 'allProperties']);

        // Route property amenities
        Route::put('/amenity/{id}/new',
            [EstateController::class, 'newPropertyAmenity']);
        Route::patch('/amenity/{id}/update/{amId}',
            [EstateController::class, 'updatePropertyAmenity']);
        Route::delete('/amenity/{id}/remove/{amId}',
            [EstateController::class, 'removePropertyAmenity']);
        Route::delete('/amenity/{id}/remove-all',
            [EstateController::class, 'removeAllPropertyAmenities']);
        Route::get('/amenity/{id}/all',
            [EstateController::class, 'getPropertyAmenities']);

        // Route Property Picture
        Route::put('/pic/{id}/add',
            [EstateController::class, 'addPropertyPictures']);
        Route::patch('/pic/{id}/update/{pic}',
            [EstateController::class, 'updatePropertyPicture']);
        Route::delete('/pic/{id}/remove/{pic}',
            [EstateController::class, 'removePropertyPicture']);
        Route::get('/pic/{id}/cat/{cat}',
            [EstateController::class, 'getPropertyPictureCategory']);

    });

    Route::prefix('location')->group(function () {
        Route::get('/cp',
            [LocationController::class, 'zipCode']);
    });
});

