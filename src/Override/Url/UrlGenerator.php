<?php

    namespace App\Services\Override\Url;

    use Illuminate\Routing\RouteCollectionInterface;
    use Illuminate\Http\Request;
    use Illuminate\Routing\UrlGenerator as BaseUrlGenerator;
    use Symfony\Component\Routing\Exception\RouteNotFoundException;
    use Utilities\Str;
    use Utilities\Url;

    class UrlGenerator extends BaseUrlGenerator
    {
        public function __construct(RouteCollectionInterface $routes, Request $request, $assetRoot = null)
        {
            parent::__construct($routes, $request, $assetRoot);
        }

        public function route($name, $parameters = [], $absolute = true)
        {
            if (!Str::isNull($route = $this->routes->getByName($name))) {

                if(enableUrlEncode()){
                    $parameters = Url::getRouteParamEncryptionDecryption($parameters, ENCRYPTED_PARAM);
                }

                return $this->toRoute($route, $parameters, $absolute);
            }

            throw new RouteNotFoundException("Route [{$name}] not defined.");
        }
    }
