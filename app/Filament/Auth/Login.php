<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\Login as LoginBase;
use Illuminate\Contracts\Support\Htmlable;

class Login extends LoginBase
{
    public function getHeading(): string|Htmlable
    {
        return 'Entra en el panel de TravelPet';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Con el correo y la contraseña de tu cuenta.';
    }
}
