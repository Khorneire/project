<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Errors\Reporting\BugsnagReporter;
use App\Errors\Reporting\ReporterInterface;

class ErrorServiceProvider extends ServiceProvider
{
    /**
     * Registers the Error Reporting system.
     */
    public function register()
    {
        // Whilst the Bugsnag SDK will only run in production, we still need to disable
        // our additional reporter which adds additional data to reports. We don't need this in any other environment.
        if (env('APP_ENV') === 'production') {
            $this->app->instance(ReporterInterface::class, new BugsnagReporter());
        }
    }
}
