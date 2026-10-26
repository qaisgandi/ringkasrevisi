<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    protected $signature = 'ringkas:create-admin';

    protected $description = 'Create an administrator account using hidden password prompts';

    public function handle(): int
    {
        $attributes = [
            'name' => trim((string) $this->ask('Nama admin')),
            'username' => trim((string) $this->ask('Username admin')),
            'email' => trim((string) $this->ask('Email admin')),
        ];

        $validator = Validator::make($attributes, [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $password = $this->secret('Password admin (minimal 12 karakter)');
        $confirmation = $this->secret('Ulangi password admin');

        if (strlen($password) < 12 || $password !== $confirmation) {
            $this->error('Password harus minimal 12 karakter dan kedua input harus sama.');

            return self::FAILURE;
        }

        User::create([
            ...$attributes,
            'password' => $password,
            'role' => 'admin',
        ]);

        $this->info('Akun admin berhasil dibuat. Simpan kata sandi di pengelola kata sandi Anda.');

        return self::SUCCESS;
    }
}
