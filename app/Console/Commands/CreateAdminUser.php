<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

#[Signature('admin:create {name} {email}')]
#[Description('Create a board administrator account')]
class CreateAdminUser extends Command
{
    public function handle(): int
    {
        $attributes = Validator::make([
            'name' => $this->argument('name'),
            'email' => $this->argument('email'),
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ])->validate();

        $password = $this->secret('Password (minimum 12 characters)');
        $passwordConfirmation = $this->secret('Confirm password');

        if (! is_string($password) || ! is_string($passwordConfirmation) || $password !== $passwordConfirmation) {
            $this->error('The passwords did not match.');

            return self::FAILURE;
        }

        $passwordLength = mb_strlen($password);

        if ($passwordLength < 12 || $passwordLength > 255) {
            $this->error('The password must be between 12 and 255 characters.');

            return self::FAILURE;
        }

        $user = new User;
        $user->fill([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => $password,
        ]);
        $user->forceFill(['is_admin' => true])->save();

        $this->info('Administrator account created.');

        return self::SUCCESS;
    }
}
