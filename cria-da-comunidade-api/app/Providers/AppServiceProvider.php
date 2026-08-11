<?php

namespace App\Providers;

use App\Models\Vaga;
use App\Observers\VagaObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Vaga::observe(VagaObserver::class);
    }
}
