<?php
	
	namespace Brrittoo\UrlEncoder;
	
	use Illuminate\Support\ServiceProvider;
	use Brrittoo\UrlEncoder\Override\Url\UrlGenerator;
	use Brrittoo\UrlEncoder\Middleware\UrlManipulationMiddleware;
	use Brrittoo\UrlEncoder\Utilities\Str;
	use RuntimeException;
	use Illuminate\Routing\Router;
	use Illuminate\Contracts\Http\Kernel;
	
	class UrlEncoderServiceProvider extends ServiceProvider
	{
		public function register()
		{
			$this->mergeConfigFrom(
				__DIR__.'/../config/url-encoder.php',
				'url-encoder'
			);
			
			$this->app->extend('url', function ($original, $app) {
				if ($this->shouldEnableUrlEncoding($app)) {
					return new UrlGenerator(
						$app['router']->getRoutes(),
						$app['request'],
						$app['config']->get('app.asset_url')
					);
				}
				return $original;
			});
		}
		
		protected function shouldEnableUrlEncoding($app)
		{
			if (!config('url-encoder.is_encoding_enable', false)) {
				return false;
			}
			
			// Don't enable in console unless we have a specific need
			if ($app->runningInConsole()) {
				return false;
			}
			
			if (!$app->bound('request') || !$app->bound('router')) {
				return false;
			}
			
			$currentRoute = $app['router']->current();
			if (!$currentRoute) {
				return false;
			}
			
			return $this->isRouteInEnabledGroups($currentRoute);
		}
		
		protected function isRouteInEnabledGroups($route)
		{
			$enabledGroups = config('url-encoder.enable_route_groups', []);
			$excludedRoutes = config('url-encoder.exclude_routes', []);
			
			// Check if route is excluded
			if ($route->getName() && in_array($route->getName(), $excludedRoutes)) {
				return false;
			}
			
			// Check if route belongs to any enabled middleware group
			$routeMiddleware = $route->gatherMiddleware();
			
			foreach ($enabledGroups as $group) {
				if (in_array($group, $routeMiddleware)) {
					return true;
				}
			}
			
			return false;
		}
		
		public function boot()
		{
			$this->validateEncryptionKey();
			$this->publishConfig();
			$this->publishFullPakages();
			
			
			
			$this->app->booted(function () {
				$this->autoRegisterMiddleware();
			});
			
		}
		
		protected function autoRegisterMiddleware()
		{
			// Only proceed if encoding is enabled
			if (!config('url-encoder.is_encoding_enable', false)) {
				return;
			}
			
			$this->app->booted(function () {
				$router = $this->app['router'];
			});
			
			$router = $this->app->make(Router::class);
			$manualRegistration = config('url-encoder.manual_middleware_registration', 'auto');
			
			// Handle manual registration settings
			if ($manualRegistration === 'none') {
				return; // Don't register anything automatically
			}
			
			if ($manualRegistration === 'global') {
				// Register middleware globally
				$this->registerGlobalMiddleware();
				return;
			}
			
			if (is_array($manualRegistration)) {
				// Register middleware to specific groups
				$this->registerToSpecificGroups($router, $manualRegistration);
				return;
			}
			
			// Default behavior: auto-register to enabled route groups
			$enabledGroups = config('url-encoder.enable_route_groups', []);
			$routeGroups = $this->getRegisteredRouteGroups();
			
			// Apply middleware to matching route groups
			foreach ($routeGroups as $groupName => $groupMiddleware) {
				if (in_array($groupName, $enabledGroups)) {
					$router->pushMiddlewareToGroup($groupName, UrlManipulationMiddleware::class);
				}
			}
		}
		
		protected function registerGlobalMiddleware()
		{
			$kernel = $this->app->make(Kernel::class);
			
			// Check if middleware is already registered by inspecting the kernel properties
			if (!$this->isMiddlewareAlreadyRegistered($kernel)) {
				$kernel->pushMiddleware(UrlManipulationMiddleware::class);
			}
		}
		
		protected function isMiddlewareAlreadyRegistered(Kernel $kernel)
		{
			// Use reflection to check if middleware is already registered
			$reflection = new \ReflectionClass($kernel);
			
			if ($reflection->hasProperty('middleware')) {
				$property = $reflection->getProperty('middleware');
				$property->setAccessible(true);
				$middleware = $property->getValue($kernel);
				
				return in_array(UrlManipulationMiddleware::class, $middleware);
			}
			
			return false;
		}
		
		protected function registerToSpecificGroups($router, array $groups)
		{
			$routeGroups = $this->getRegisteredRouteGroups();
		
			
			foreach ($groups as $groupName) {
				if (isset($routeGroups[$groupName])) {
					// Check if middleware is already in the group
					if (!in_array(UrlManipulationMiddleware::class, $routeGroups[$groupName])) {
					
						$router->pushMiddlewareToGroup($groupName, UrlManipulationMiddleware::class);
					}
				}
			}
		}
		
		protected function getRegisteredRouteGroups()
		{
			$groups = [];
			
			// Get route groups from the router
			$router = $this->app->make(Router::class);
			$reflection = new \ReflectionClass($router);
			
			if ($reflection->hasProperty('middlewareGroups')) {
				$property = $reflection->getProperty('middlewareGroups');
				$property->setAccessible(true);
				$groups = $property->getValue($router);
			}
			
			return $groups;
		}
		
		protected function validateEncryptionKey()
		{
			if ($this->app->configurationIsCached()) {
				return;
			}
			
			$key = config('url-encoder.url_encryption_secret_key');
			
			if (empty($key)) {
				$key = Str::bin2hex(Str::randomBytes(16));
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
			
			if (Str::strlen($key) < 32) {
				throw new RuntimeException(
					'URL_ENCRYPTION_SECRET_KEY must be at least 32 characters. '.
					'Current length: '.Str::strlen($key)
				);
			}
		}
		
		
		protected function publishConfig()
		{
			$this->publishes([
				__DIR__.'/../config/url-encoder.php' => config_path('url-encoder.php'),
			], 'url-encoder-config');
		}
		
		protected function publishFullPakages()
		{
			// Publish full package source for customization
			$this->publishes([
				__DIR__ . '/../' => base_path('packages/brrittoo/url-encoder'),
			], 'url-encoder-source');
		}
	}