<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EmailTrackerController;

Route::get('/', [HomeController::class, 'dashboard'])->name('dashboard');

Route::get('/mailing', [HomeController::class, 'mailing'])->name('mailing');

Route::get('/suscribe', [HomeController::class, 'suscribe'])->name('suscribe');

Route::prefix('email')->name('email.')->group(function () {
    Route::get('/open/{email}', [EmailTrackerController::class, 'open'])->name('open');
    Route::get('/click/{email}', [EmailTrackerController::class, 'click'])->name('click');
    Route::get('/unsubscribe/{email}', [EmailTrackerController::class, 'unsubscribe'])->name('unsubscribe');
});

Route::get('/test-email', function () {

    Mail::raw('Ceci est un test avec MailerSend', function ($message) {
        $message->to('webmasterthriller@gmail.com')
                ->subject('Test MailerSend');
    });

    return 'Email envoyé !';
});
