<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    protected $signature = 'skec:create-admin {email} {name} {--password=}';
    protected $description = 'Create a new admin user for SKEC platform';

    public function handle(): int
    {
        $email    = $this->argument('email');
        $name     = $this->argument('name');
        $password = $this->option('password') ?? $this->secret('Enter password (min 8 chars)');

        $validator = Validator::make(compact('email', 'name', 'password'), [
            'email'    => ['required', 'email', 'unique:users,email'],
            'name'     => ['required', 'string', 'min:2'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return Command::FAILURE;
        }

        $admin = User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
            'role'     => 'admin',
            'status'   => 'active',
        ]);

        $this->info("✓ Admin created: {$admin->name} <{$admin->email}>");
        return Command::SUCCESS;
    }
}
