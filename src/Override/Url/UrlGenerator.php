<?php

    namespace Brritto\UrlEncoder\Override\Url;

    use Illuminate\Routing\RouteCollectionInterface;
    use Illuminate\Http\Request;
    use Illuminate\Routing\UrlGenerator as BaseUrlGenerator;
    use Brritto\UrlEncoder\Utilities\Arr;
    use Brritto\UrlEncoder\Utilities\Str;
    use Brritto\UrlEncoder\Utilities\Url;
    use Symfony\Component\Routing\Exception\RouteNotFoundException;


    class UrlGenerator extends BaseUrlGenerator
    {
        public function __construct(RouteCollectionInterface $routes, Request $request, $assetRoot = null)
        {
            parent::__construct($routes, $request, $assetRoot);
        }
	    
	    public function route($name, $parameters = [], $absolute = true)
	    {
		    $route = $this->routes->getByName($name);
		    if (Str::isNull($route)) {
			    throw new RouteNotFoundException("Route [{$name}] not defined.");
		    }
		    $excludedRoutes = config('url-encoder.exclude_routes', []);
		    if (enableUrlEncode() && !empty($parameters) && !Arr::inArray($name,
				    $excludedRoutes)) {
			    $parameters = Url::getRouteParamEncryptionDecryption($parameters, ENCRYPTED_PARAM);
		    }
		    
		    return $this->toRoute($route, $parameters, $absolute);
	    }

    }
