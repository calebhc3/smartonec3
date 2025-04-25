<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Mail\NotificacaoShopeeRetorno;
use Illuminate\Support\Facades\Mail;


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
    return redirect()->route('login'); // Redireciona para a rota de login
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

Route::redirect('/admin/login', '/login')->name('filament.admin.login');
Route::redirect('/admin/logout', '/logout')->name('filament.admin.logout');
Route::redirect('/admin/register', '/register')->name('filament.admin.register');

Route::get('/teste-email', function () {
    Mail::to('seu-email@exemplo.com')->send(new NotificacaoShopeeRetorno(5));
    return 'E-mail enviado com sucesso!';
});