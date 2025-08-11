<?php

    namespace ParamGuard\UrlEncoder\Middleware;

    use Illuminate\Http\Request;

    use Closure;
    use ParamGuard\UrlEncoder\Utilities\Arr;
    use ParamGuard\UrlEncoder\Utilities\Url;
    use Symfony\Component\HttpFoundation\Response;
    class UrlManipulationMiddleware
    {

        /**
         * Handle an incoming request.
         *
         * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
         */
        public function handle(Request $request, Closure $next) : Response
        {
            if (enableUrlEncode()) {
				
                if (!$request->route()) {
                    return $next($request);
                }
                $parameters = $request->route()->parameters();
                if (!empty($parameters) && Arr::accessible($parameters)) {
                    $decrypted_parameters = Url::getRouteParamEncryptionDecryption($parameters, DECRYPTED_PARAM);

                    foreach ($decrypted_parameters as $key => $value) {
                        $request->route()->setParameter($key, $value);
                    }
                }
            }

            return $next($request);
        }
	}
