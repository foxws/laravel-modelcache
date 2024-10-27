<?php

namespace Foxws\UserCache\Commands;

use Foxws\UserCache\Facades\UserCache;
use Illuminate\Console\Command;

class ClearCommand extends Command
{
    protected $signature = 'usercache:clear';

    protected $description = 'Clear the user cache';

    public function handle()
    {
        $this->clear();

        $this->info('User cache cleared!');
    }

    protected function clear()
    {
        UserCache::clear();
    }
}
