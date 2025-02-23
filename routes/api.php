<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\JournalController;
use App\Http\Controllers\Api\VacancyController;
use App\Http\Controllers\Api\PresenceController;
use App\Http\Controllers\Api\ApplianceController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PresenceStatusController;
use App\Http\Controllers\Api\SavedVacancyController;

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

Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::resource('/faqs', FaqController::class)->only('index');
});

Route::middleware('auth:sanctum', 'auth.check_status')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);
    Route::put('/store-fcm-token', [AuthController::class, 'storeFcmToken']);

    Route::put('/change-profile', [UserController::class, 'update']);
    Route::post('/avatars', [UserController::class, 'uploadAvatar']);
    Route::post('/resumes', [UserController::class, 'uploadResume']);

    Route::get('/news', [NewsController::class, 'index']);
    Route::get('/news/{id}', [NewsController::class, 'show']);

    Route::get('/vacancies', [VacancyController::class, 'index']);
    Route::get('/vacancies/recommended', [VacancyController::class, 'recommended']);
    Route::get('/vacancies/{id}', [VacancyController::class, 'show']);
    Route::get('/search/{searchbar}', [VacancyController::class, 'search']);

    Route::resource('appliances', ApplianceController::class)->only([
        'index', 'store', 'destroy'
    ]);
    Route::get('/appliances/accepted', [ApplianceController::class, 'accepted']);
    Route::put('/appliances/{id}/cancel', [ApplianceController::class, 'cancel']);
    Route::put('/appliances/{id}/edit-date', [ApplianceController::class, 'editDate']);

    Route::resource('savedvacancies', SavedVacancyController::class)->only([
        'index', 'store', 'destroy'
    ]);

    Route::resource('journals', JournalController::class)->except(['create', 'edit']);

    Route::post('export-journal/{id}', [ExportController::class, 'pdfSingleCompany']);
    Route::post('export-journals', [ExportController::class, 'pdfMultipleCompany']);
    Route::post('export-certificate/{id}', [ExportController::class, 'exportCertificate']);

    Route::get('/today-activities', [PresenceController::class, 'todayActivity']);
    Route::resource('presences', PresenceController::class)->except(['create', 'edit']);

    Route::get('/presence-statuses', [PresenceStatusController::class, 'index']);
    Route::get('/presence-statuses/{id}', [PresenceStatusController::class, 'show']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/mark-as-read', [NotificationController::class, 'markAsRead']);
});