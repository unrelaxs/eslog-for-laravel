<?php

namespace Unrelaxs\Eslog;

use Illuminate\Support\ServiceProvider;
use Unrelaxs\Eslog\Console\ElasticLogMappingCommand;

class EslogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            ElasticLogMappingCommand::class
        ]);
        $this->loadMigrationsFrom([
            __DIR__.'/database/migrations/2020_03_26_060835_create_logs_table.php'
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
