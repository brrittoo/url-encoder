<?php
	
	namespace ParamGuard\UrlEncoder;
	
	use Illuminate\Support\ServiceProvider;
	use ParamGuard\UrlEncoder\Override\Url\UrlGenerator;
	use ParamGuard\UrlEncoder\Middleware\UrlManipulationMiddleware;
	use RuntimeException;
	use Illuminate\Routing\Router;
	
	class UrlEncoderServiceProvider extends ServiceProvider
	{
		public function register()
		{
			$this->mergeConfigFrom(
				__DIR__.'/../config/url-encoder.php',
				'url-encoder'
			);
			
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
			$this->validateEncryptionKey();
			$this->registerMiddleware();
			$this->publishConfig();
		}
		
		protected function validateEncryptionKey()
		{
			if ($this->app->configurationIsCached()) {
				return;
			}
			
			$key = config('url-encoder.url_encryption_secret_key');
			
			if (empty($key)) {
				$key = bin2hex(random_bytes(16));
				if ($this->app->environment(['local', 'testing', 'development'])) {
					
					config(['url-encoder.url_encryption_secret_key' => $key]);
					$this->app['log']->warning('Auto-generated temporary URL encryption key: '.$key);
				} else {
					throw new RuntimeException(
						"URL_ENCRYPTION_SECRET_KEY is not set. \n".
						"We tried to generate one but couldn't write to .env file. \n".
						"Please add this to your .env manually:\n".
						"URL_ENCRYPTION_SECRET_KEY=\"{$key}\""
					);
				}
			}
			
			if (strlen($key) < 32) {
				throw new RuntimeException(
					'URL_ENCRYPTION_SECRET_KEY must be at least 32 characters. '.
					'Current length: '.strlen($key)
				);
			}
		}
		
		protected function registerMiddleware()
		{
			$router = $this->app->make(Router::class);
			$middlewareAlias = config('url-encoder.middleware_alias', 'url-encode');
			$enabledGroups = config('url-encoder.enable_route_groups', []);
			
			$router->aliasMiddleware($middlewareAlias, UrlManipulationMiddleware::class);
			
			$reflection = new \ReflectionObject($router);
			$property = $reflection->getProperty('middlewareGroups');
			$property->setAccessible(true);
			$middlewareGroups = $property->getValue($router);
			
			foreach ($enabledGroups as $groupName) {
				if (isset($middlewareGroups[$groupName]) &&
					!in_array($middlewareAlias, $middlewareGroups[$groupName])) {
					$middlewareGroups[$groupName][] = $middlewareAlias;
				}
			}
			
			$property->setValue($router, $middlewareGroups);
		}
		
		protected function publishConfig()
		{
			$this->publishes([
				__DIR__.'/../config/url-encoder.php' => config_path('url-encoder.php'),
			], 'url-encoder-config');
		}
	}