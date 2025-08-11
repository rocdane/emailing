<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-email', function () {
    Mail::raw('Ceci est un test avec MailerSend', function ($message) {
        $message->to('webmasterthriller@gmail.com')
                ->subject('Test MailerSend');
    });
    return 'Email envoyé !';
});

Route::get('/suscribe', function () {
    return 'You susbribe !';
});
