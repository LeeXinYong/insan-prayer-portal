<?php

use App\Http\Controllers\API\BannerController;
use App\Http\Controllers\API\BrochureController;
use App\Http\Controllers\API\NewsController;
use App\Http\Controllers\API\VideoController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\SampleDataController;
use App\Services\APIHashService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

// Sample API route
Route::get('/profits', [SampleDataController::class, 'profits'])->name('profits');

Route::post('/register', [RegisteredUserController::class, 'apiStore']);

Route::post('/login', [AuthenticatedSessionController::class, 'apiStore']);

Route::post('/forgot_password', [PasswordResetLinkController::class, 'apiStore']);

Route::post('/verify_token', [AuthenticatedSessionController::class, 'apiVerifyToken']);

Route::get('/users', [SampleDataController::class, 'getUsers']);


Route::middleware(["verify_api_key"])->group(function () {
    // Banner API routes
    Route::get("/banners", [BannerController::class, "getBanners"]);

    // Brochure API routes
    Route::get("/brochures", [BrochureController::class, "getBrochures"]);

    // News API routes
    Route::get("/news", [NewsController::class, "getNews"]);

    // Video API routes
    Route::get("/videos", [VideoController::class, "getVideos"]);
});

/*
 * FOR LOCAL ONLY
 */
if (App::isLocal()) {
    Route::get("/get_hash", [APIHashService::class, "getHash"]);
    Route::post("/get_hash", [APIHashService::class, "getHash"]);
    Route::put("/get_hash", [APIHashService::class, "getHash"]);
}
