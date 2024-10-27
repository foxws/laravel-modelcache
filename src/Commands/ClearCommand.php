<?php

namespace Foxws\UserCache\Commands;

use Foxws\UserCache\Facades\UserCache;
use Illuminate\Console\Command;
use Illuminate\Foundation\Auth\User;

class ClearCommand extends Command
{
    protected $signature = 'usercache:clear {user?} {--key=}';

    protected $description = 'Clear the user cache';

    public function handle()
    {
        $this->clear();

        $this->components->info('User cache cleared!');
    }

    protected function clear()
    {
        if ($key = $this->option('key')) {
            $user = User::findOrFail($this->argument('user'));

            return UserCache::forget($user, $key);
        }

        UserCache::clear();
    }
}
