<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:promote-admin {email?}', function (?string $email = null) {
    if ($email) {
        $user = User::where('email', $email)->first();
    } elseif (User::where('role', User::ROLE_ADMIN)->doesntExist() && User::count() === 1) {
        $user = User::first();
    } else {
        $this->error('Indica el correo registrado que quieres promover.');

        return 1;
    }

    if (! $user) {
        $this->error('No existe una cuenta registrada con ese correo.');

        return 1;
    }

    if ($user->isAdmin()) {
        $this->info('La cuenta ya tiene el rol de administrador.');

        return 0;
    }

    $user->forceFill(['role' => User::ROLE_ADMIN])->save();
    $this->info('Cuenta promovida a administrador.');

    return 0;
})->purpose('Promote an existing account to administrator without changing its password');

Artisan::command('app:reset-admin-password {email?}', function (?string $email = null) {
    $admins = User::where('role', User::ROLE_ADMIN);
    $user = $email
        ? $admins->where('email', $email)->first()
        : ($admins->count() === 1 ? $admins->first() : null);

    if (! $user) {
        $this->error('No se encontró un administrador único. Indica el correo de la cuenta admin.');

        return 1;
    }

    $password = $this->secret('Nueva contraseña (mínimo 8 caracteres)');
    $confirmation = $this->secret('Confirma la nueva contraseña');

    if (! is_string($password) || mb_strlen($password) < 8) {
        $this->error('La contraseña debe tener al menos 8 caracteres.');

        return 1;
    }

    if ($password !== $confirmation) {
        $this->error('Las contraseñas no coinciden.');

        return 1;
    }

    $user->password = $password;
    $user->save();

    $this->info('Contraseña del administrador actualizada.');

    return 0;
})->purpose('Reset an administrator password using hidden terminal prompts');
