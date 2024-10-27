<?php

namespace Foxws\UserCache\Commands;

use Illuminate\Console\Command;

class UserCacheCommand extends Command
{
    public $signature = 'laravel-user-cache';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
