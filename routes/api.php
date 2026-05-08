<?php

use App\Http\Controllers\Api\ReferralApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
| Referral module API (superadmin referral data — JSON only; web Blade untouched)
| Auth: POST /api/referral/login with { "username", "password" } → Bearer token
| Then: Authorization: Bearer {token}
*/
Route::post('/referral/login', [ReferralApiController::class, 'login']);

Route::middleware('auth:sanctum')->prefix('referral')->group(function () {
    Route::post('/logout', [ReferralApiController::class, 'logout']);
    Route::get('/me', [ReferralApiController::class, 'me']);

    Route::get('/doctors', [ReferralApiController::class, 'doctors']);
    Route::get('/doctors/{id}', [ReferralApiController::class, 'doctor']);

    Route::get('/meetings', [ReferralApiController::class, 'meetings']);
    Route::get('/patients', [ReferralApiController::class, 'patients']);

    Route::get('/locations', [ReferralApiController::class, 'locations']);
    Route::get('/zones', [ReferralApiController::class, 'zones']);
    Route::get('/marketers', [ReferralApiController::class, 'marketers']);

    Route::get('/branches-legacy', [ReferralApiController::class, 'branchesLegacy']);
    Route::get('/zones-legacy', [ReferralApiController::class, 'zonesLegacy']);
});
