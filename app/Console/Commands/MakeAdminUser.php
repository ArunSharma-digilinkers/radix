<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

/**
 * Provisions the first (or any subsequent) real admin account.
 *
 * Not a seeder — seeders are banned from creating real accounts/content
 * (CLAUDE.md §1). This is an explicitly-invoked operation where a human
 * supplies real credentials interactively, the same category of thing as
 * running `php artisan migrate` by hand.
 */
class MakeAdminUser extends Command
{
    protected $signature = 'radix:make-admin';

    protected $description = 'Create a super-admin user account';

    public function handle(): int
    {
        $name = $this->ask('Name');
        $email = $this->ask('Email');

        if (User::where('email', $email)->exists()) {
            $this->error("A user with email [{$email}] already exists.");

            return self::FAILURE;
        }

        $password = $this->secret('Password (min 8 characters)');
        $confirmation = $this->secret('Confirm password');

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $password, 'password_confirmation' => $confirmation],
            ['name' => ['required', 'string'], 'email' => ['required', 'email'], 'password' => ['required', 'string', 'min:8', 'confirmed']]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $user->assignRole('super-admin');

        $this->info("Created super-admin [{$email}].");

        return self::SUCCESS;
    }
}
