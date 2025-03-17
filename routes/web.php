<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('filament.admin.auth.')->middleware(['auth'])->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('email-verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/admin'); // Redireciona para o painel Filament
    })->middleware(['signed'])->name('email-verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'E-mail de verificação enviado!');
    })->middleware(['throttle:6,1'])->name('email-verification.send');
});

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect('/admin'); // Redireciona após o logout
    })->name('dashboard');
});

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login'); // Redireciona após o logout
})->name('logout');

Route::redirect('/auth/login', '/login', 301);
