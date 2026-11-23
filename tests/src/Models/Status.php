<?php

declare(strict_types=1);

namespace Foxws\ModelCache\Tests\Models;

enum Status: string
{
    case Draft = 'draft';
    case Published = 'published';
}
