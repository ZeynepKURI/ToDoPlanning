<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('/jobs/{id}', [JobController::class, 'show']);
Route::put('/jobs/{id}', [JobController::class, 'update']);
Route::delete('/jobs/{id}', [JobController::class, 'destroy']);
Route::get('/jobs/calculate', [JobController::class, 'calculate']);
Route::post('/jobs', [JobController::class, 'store']); // Yeni iş eklemek için
Route::get('/jobs', [JobController::class, 'index']); // İşleri listelemek için
Route::get('/jobs', [JobController::class, 'fetchJobs']);
Route::post('/jobs/distribute', [JobController::class, 'distributeJobs']);
