<?php

    namespace ParamGuard\UrlEncoder;

    use Illuminate\Support\ServiceProvider;
    use ParamGuard\UrlEncoder\Override\Url\UrlGenerator;
    use ParamGuard\UrlEncoder\Middleware\UrlManipulationMiddleware;
    use ParamGuard\UrlEncoder\Utilities\Arr;
    
    class UrlEncoderServiceProvider extends ServiceProvider
    {
        public function register()
        {
            $this->app->extend('url', function ($original) {
                return new UrlGenerator(
                    $this->app['router']->getRoutes(),
                    $this->app['request'],
                    $this->app['config']->get('app.asset_url')
                );
            });
        }


	        
	        
		public function boot()
        {
	        $router = $this->app['router'];
	        $middlewareAlias = config('url-encoder.middleware_alias', 'url-encode');
	        $enabledGroups = config('url-encoder.enable_route_groups', []);
	        $router->aliasMiddleware($middlewareAlias, UrlManipulationMiddleware::class);
	        $refObject = new \ReflectionObject($router);
	        $refProperty = $refObject->getProperty('middlewareGroups');
	        $refProperty->setAccessible(true);
	        $middlewareGroups = $refProperty->getValue($router);
	        foreach ($enabledGroups as $groupName) {
		        if (isset($middlewareGroups[$groupName])) {
			        if (!Arr::inArray($middlewareAlias, $middlewareGroups[$groupName])) {
				        $middlewareGroups[$groupName][] = $middlewareAlias;
			        }
		        }
	        }
			
	        $refProperty->setValue($router, $middlewareGroups);
	        
	        $this->publishes([
		        __DIR__.'/../config/url-encoder.php' => config_path('url-encoder.php'),
	        ], 'url-encoder-config');
        }
        
    }
