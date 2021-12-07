<?php

namespace App\Tests\Fixtures;

use App\Tests\Fixtures\Domain\TodoBuilder;

function aTodo(): TodoBuilder
{
    return new TodoBuilder();
}