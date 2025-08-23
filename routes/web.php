<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EmailTrackingController;
use App\Livewire\Dashboard;
use App\Livewire\MailingForm;
use App\Livewire\EmailCampaignProgress;

Route::get('/', [HomeController::class, 'welcome'])->name('welcome');

Route::get('/suscribe', [HomeController::class, 'suscribe'])->name('suscribe');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
});

Route::prefix('email')->name('email.')->group(function () {
    Route::get('/campaign/create', MailingForm::class)->name('campaign.create');
    Route::get('/campaign/{campaign}/progress', EmailCampaignProgress::class)->name('campaign.progress');
    Route::get('/tracking/pixel/{token}', [EmailTrackingController::class, 'track_pixel'])->name('tracking.pixel');
    Route::get('/tracking/click/{token}', [EmailTrackingController::class, 'track_click'])->name('tracking.click');
    Route::get('/tracking/unsuscribe/{token}', [EmailTrackingController::class, 'track_unsuscribe'])->name('tracking.unsuscribe');
});
