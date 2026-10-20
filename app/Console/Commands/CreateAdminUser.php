<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autocare:create-admin 
                            {--name= : The name of the administrator}
                            {--email= : The email address of the administrator}
                            {--password= : The administrator password (optional, prompted securely if omitted)}
                            {--phone= : The contact phone number (optional)}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Create a secure initial Administrator account for AutoCare System';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('=============================================');
        $this->info('   AutoCare System - Administrator Setup');
        $this->info('=============================================');

        $name = $this->option('name') ?: $this->ask('Administrator Full Name');
        $email = $this->option('email') ?: $this->ask('Administrator Email Address');

        // Validation
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error("Validation Error: {$error}");
            }

            return Command::FAILURE;
        }

        $password = $this->option('password');
        if (empty($password)) {
            $password = $this->secret('Administrator Password (minimum 8 characters)');
            $passwordConfirmation = $this->secret('Confirm Administrator Password');

            if ($password !== $passwordConfirmation) {
                $this->error('Error: Passwords do not match.');

                return Command::FAILURE;
            }
        }

        if (strlen($password) < 8) {
            $this->error('Error: Password must be at least 8 characters in length.');

            return Command::FAILURE;
        }

        $phone = $this->option('phone') ?: $this->ask('Contact Phone (Optional)', null);

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make($password),
            'role' => 'admin',
            'status' => 'active',
        ]);

        ActivityLog::log(
            action: 'created',
            description: "Administrator {$user->name} ({$user->email}) created via Artisan console command.",
            module: 'Administration',
            userId: $user->id
        );

        $this->newLine();
        $this->info("✓ Administrator [{$user->name}] with email [{$user->email}] created successfully!");
        $this->info('You may now log in to AutoCare System with these credentials.');
        $this->newLine();

        return Command::SUCCESS;
    }
}
