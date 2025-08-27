<?php
	
	namespace Brritto\UrlEncoder;
	
	use Illuminate\Support\ServiceProvider;
	use Brritto\UrlEncoder\Override\Url\UrlGenerator;
	use Brritto\UrlEncoder\Middleware\UrlManipulationMiddleware;
	use Brritto\UrlEncoder\Utilities\Arr;
	use Brritto\UrlEncoder\Utilities\Str;
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
			$this->publishConfig();
			$this->publishFullPakages();
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
				__DIR__ . '/../' => base_path('packages/brritto/url-encoder'),
			], 'url-encoder-source');
		}
	}