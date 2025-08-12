<?php

    namespace ParamGuard\UrlEncoder\Override\Url;

    use Illuminate\Routing\RouteCollectionInterface;
    use Illuminate\Http\Request;
    use Illuminate\Routing\UrlGenerator as BaseUrlGenerator;
    use ParamGuard\UrlEncoder\Utilities\Arr;
    use ParamGuard\UrlEncoder\Utilities\Str;
    use ParamGuard\UrlEncoder\Utilities\Url;
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
		    
		    $enabledGroups = config('url-encoder.enable_route_groups', []);
		    $routeMiddleware = $route->getAction()['middleware'] ?? [];
			
		    $isInEnabledGroup = !empty(array_intersect($enabledGroups, (array)$routeMiddleware));
			
		    $excludedRoutes = config('url-encoder.exclude_routes', []);
		    if (enableUrlEncode() && !empty($parameters) && $isInEnabledGroup && !Arr::inArray($name,
				    $excludedRoutes)) {
			    $parameters = Url::getRouteParamEncryptionDecryption($parameters, ENCRYPTED_PARAM);
		    }
		    
		    return $this->toRoute($route, $parameters, $absolute);
	    }

    }
