<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create {--reset : Reset existing admin password}';
    protected $description = 'Create or reset admin user credentials';

    public function handle()
    {
        $adminEmail = 'admin@easypass.com';
        $adminPassword = 'admin123';
        
        $admin = User::where('email', $adminEmail)->first();
        
        if ($admin && !$this->option('reset')) {
            $this->info('Admin user already exists!');
            $this->info('Email: ' . $adminEmail);
            $this->warn('Use --reset flag to reset the password');
            return;
        }
        
        if ($admin) {
            // Reset existing admin
            $admin->password = Hash::make($adminPassword);
            $admin->role = 'admin';
            $admin->save();
            $this->info('Admin password has been reset!');
        } else {
            // Create new admin
            User::create([
                'name' => 'Admin User',
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
            $this->info('Admin user created successfully!');
        }
        
        $this->info('Admin Credentials:');
        $this->info('Email: ' . $adminEmail);
        $this->info('Password: ' . $adminPassword);
        $this->warn('Please change the password after first login!');
    }
}
