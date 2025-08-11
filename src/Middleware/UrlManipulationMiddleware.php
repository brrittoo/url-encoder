<?php

    namespace Middleware;

    use Illuminate\Http\Request;
    use Utilities\Arr;
    use Utilities\Url;
    use Closure;
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
                $parameters = $request->route()->parameters();
                if (!empty($parameters) && Arr::isArr($parameters)) {

                    $decrypted_parameters = Url::getRouteParamEncryptionDecryption($parameters, DECRYPTED_PARAM);

                    foreach ($decrypted_parameters as $key => $value) {
                        $request->route()->setParameter($key, $value);
                    }
                }
            }

            return $next($request);
        }
	}
