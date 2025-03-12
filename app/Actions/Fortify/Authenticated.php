<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Actions\RedirectIfAuthenticated;
use Illuminate\Http\Request;

class Authenticate
{
    public function authenticated(Request $request, $user)
    {
        return redirect()->route('filament.pages.welcome'); // Redireciona para o painel do Filament (/admin)
    }
}
