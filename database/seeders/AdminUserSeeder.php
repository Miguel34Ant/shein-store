<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use InvalidArgumentException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('admin.email');
        $password = config('admin.password');

        if (! $email && ! $password) {
            return;
        }

        if (! $email || ! $password || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Configura ADMIN_EMAIL válido y ADMIN_PASSWORD antes de crear el administrador.');
        }

        $user = User::firstOrNew(['email' => $email]);
        $user->name = config('admin.name') ?: 'Administrador';
        $user->password = $password;
        $user->forceFill(['role' => User::ROLE_ADMIN])->save();
    }
}