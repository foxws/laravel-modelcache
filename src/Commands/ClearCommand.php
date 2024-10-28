<?php

namespace Foxws\ModelCache\Commands;

use Foxws\ModelCache\Facades\ModelCache;
use Illuminate\Console\Command;
use Illuminate\Foundation\Auth\User;

class ClearCommand extends Command
{
    protected $signature = 'modelcache:clear {user?} {--key=}';

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

            return ModelCache::forget($user, $key);
        }

        ModelCache::clear();
    }
}
