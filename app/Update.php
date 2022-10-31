<?php

namespace App;

use MadWeb\Initializer\Contracts\Runner;

class Update
{
    public function production(Runner $run): void
    {
        $run->artisan('initialize:listing-categories');
    }

    public function local(Runner $run): void
    {
        $run->artisan('initialize:listing-categories');
    }
}
