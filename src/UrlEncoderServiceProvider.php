<?php
	
	namespace ParamGuard\UrlEncoder;
	
	use Illuminate\Support\Facades\Blade;
	use Illuminate\Support\ServiceProvider;
	use ParamGuard\UrlEncoder\Override\Url\UrlGenerator;
	use ParamGuard\UrlEncoder\Middleware\UrlManipulationMiddleware;
	use ParamGuard\UrlEncoder\Utilities\EncodeClipper;
	
	class UrlEncoderServiceProvider extends ServiceProvider
	{
		/**
		 * Register any application services.
		 *
		 * @return void
		 */
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
		
		/**
		 * Bootstrap any application services.
		 *
		 * @return void
		 */
		public function boot()
		{
			
			$key = config('url-encoder.url_encryption_secret_key');
			
			if (empty($key)) {
				if (app()->environment('local', 'testing')) {
					$key = bin2hex(random_bytes(16));
					config(['url-encoder.url_encryption_secret_key' => $key]);
					$this->app['log']->info('Generated temporary URL encryption key: '.$key);
				} else {
					throw new RuntimeException(
						'URL_ENCRYPTION_SECRET_KEY is not configured. '.
						'Please add to your .env file: '.
						'URL_ENCRYPTION_SECRET_KEY='.bin2hex(random_bytes(16))
					);
				}
			}
			
			// Register middleware
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
					if (!in_array($middlewareAlias, $middlewareGroups[$groupName])) {
						$middlewareGroups[$groupName][] = $middlewareAlias;
					}
				}
			}
			
			$refProperty->setValue($router, $middlewareGroups);
			
			// Publish configuration
			$this->publishes([
				__DIR__.'/../config/url-encoder.php' => config_path('url-encoder.php'),
			], 'url-encoder-config');
			
			// Register Blade directives
			$this->registerBladeDirectives();
		}
		
		/**
		 * Register the Blade directives.
		 *
		 * @return void
		 */
		protected function registerBladeDirectives()
		{
			Blade::directive('encrypt', function ($expression) {
				return "<?php echo \\ParamGuard\\UrlEncoder\\Utilities\\EncodeClipper::customEncryptionDecryption($expression, 'encrypt', true); ?>";
			});
			
			Blade::directive('decrypt', function ($expression) {
				return "<?php echo \\ParamGuard\\UrlEncoder\\Utilities\\EncodeClipper::customEncryptionDecryption($expression, 'decrypt', true); ?>";
			});
		}
	}