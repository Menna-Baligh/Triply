<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user['name'] = $this->ask('What is the user\'s name?');
        $user['email'] = $this->ask('What is the user\'s email?');
        $user['password'] = $this->secret('What is the user\'s password?');

        $roleName = $this->choice(
            'What is the user\'s role?',
            ['admin', 'editor'],
            1
        );
        $role = Role::where('name', $roleName)->first();
        if (! $role) {
            $this->error('Role not found.');

            return -1;
        }

        $validator = Validator::make($user, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:5',
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return -1;
        }

        DB::transaction(function () use ($user, $role) {
            $user['password'] = Hash::make($user['password']);
            $newUser = User::create($user);
            $newUser->roles()->attach($role->id);
            $this->info('User created successfully.');
        });
    }
}
