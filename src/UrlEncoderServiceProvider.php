<?php

    use App\Services\Override\Url\UrlGenerator;
    use Illuminate\Support\ServiceProvider;

    class UrlEncoderServiceProvider extends ServiceProvider
	{
        /*
         *
         *  Override the UrlGenerator Class
         */
        public function register()
        {

            app()->extend('url', function () {

                return new UrlGenerator(
                    app('router')->getRoutes(),
                    request(),
                    app('config')->get('app.asset_url')
                );

            });

        }

        public function boot()
        {

        }
	}
