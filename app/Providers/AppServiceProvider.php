<?php

namespace App\Providers;

use App\Helpers\TimeHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive('quantity', function ($expression) {
            return "<?php echo \App\Services\QuantityFormatter::format({$expression}); ?>";
        });

        // Sri Lanka timezone helpers
        Carbon::macro('toSriLanka', function () {
            return $this->copy()->setTimezone(config('app.display_timezone', 'Asia/Colombo'));
        });

        Blade::directive('srilankatime', function ($expression) {
            return "<?php echo \App\Helpers\TimeHelper::format({$expression}); ?>";
        });

        Blade::directive('srilankadate', function ($expression) {
            return "<?php echo \App\Helpers\TimeHelper::formatDate({$expression}); ?>";
        });
    }
}

