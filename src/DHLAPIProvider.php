<?php

    namespace App\Providers;

    use Illuminate\Support\ServiceProvider;
    use MisterBrownRSA\DHL\API\GetCapability;
    use MisterBrownRSA\DHL\API\GetQuote;

    class DHLAPIProvider extends ServiceProvider
    {
        protected $defer = TRUE;

        /**
         * Bootstrap the application services.
         *
         * @return void
         */
        public function boot()
        {
            $this->publishes([
                __DIR__ . "/config/dhl.php" => config_path('dhl.php'),
            ]);
        }

        /**
         * Register the application services.
         *
         * @return void
         */
        public function register()
        {
            $this->app->singleton(GetQuote::class, function ($app) {
                return new GetQuote();
            });

            $this->app->singleton(GetCapability::class, function ($app) {
                return new GetCapability();
            });
        }

        public function provides()
        {
            return [GetQuote::class, GetCapability::class];
        }
    }
