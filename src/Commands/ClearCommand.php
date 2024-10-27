<?php

namespace Foxws\UserCache\Commands;

use Foxws\UserCache\Facades\UserCache;
use Illuminate\Console\Command;

class ClearCommand extends Command
{
    protected $signature = 'usercache:forget {--key=}';

    protected $description = 'Clear the user cache';

    public function handle()
    {
        $this->clear();

        $this->info('User cache cleared!');
    }

    protected function clear()
    {
        if ($key = $this->option('key')) {
            return UserCache::forget($key);
        }

        UserCache::clear();
    }
}
