<?php

use App\Http\Controllers\DemoAutoUpdateController;
use App\Http\Controllers\ClientAutoUpdateController;

use App\Http\Controllers\LicenseController;
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

Route::controller(DemoAutoUpdateController::class)->group(function () {
    Route::get('fetch-data-general', 'fetchDataGeneral')->name('fetch-data-general');
    Route::get('fetch-data-upgrade', 'fetchDataForAutoUpgrade')->name('data-read');
    Route::get('fetch-data-bugs', 'fetchDataForBugs')->name('fetch-data-bugs');
});

/*Route::controller(LicenseController::class)->group(function () {
    Route::post('fetch-data-license', 'fetchDataLicense');
});*/
Route::post('fetch-data-license', [ClientAutoUpdateController::class, 'fetchDataLicense'])->name('fetch-data-license');