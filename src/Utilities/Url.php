<?php

    namespace ParamGuard\UrlEncoder\Utilities;

	use PHPUnit\Util\Exception;
	
	class Url
	{
        public static function parse($url, $component = -1): array
        {
            $parsed_url_query = parse_url($url, $component);
            $query_array = array();
            parse_str($parsed_url_query, $query_array);
            return $query_array;
        }


        public static function buildQuery($params){
            return http_build_query($params);
        }

        public static function isValid($url){
            return Str::contains($url, 'https://') || Str::contains($url, 'http://');
        }

        public static function baseName($url){
            return basename($url);
        }

        public static function removeSlashes($url){
            if(self::isValid($url)){
                $separator = '://';
                $explode = explode($separator,$url);
                if (Arr::accessible($explode)){
                    $url = $explode[0].$separator.preg_replace('/(\/+)/','/',$explode[1]);
                }
            }
            return $url;
        }
        public static  function hostName($url){
            $parsed_url = parse_url($url);
            return $parsed_url['host'] ?? null;
        }

        public static function getRouteParamEncryptionDecryption($parameters, $type)
        {

            $data_cipher_type = ENCRYPTED_PARAM;
            if($type == DECRYPTED_PARAM){
                $data_cipher_type = DECRYPTED_PARAM;
            }

            try{

                if(!empty($parameters) && Arr::accessible($parameters)){
                    foreach ($parameters as $key => $parameter){
                        $parameters[$key] = EncodeClipper::customEncryptionDecryption(
                            $parameter,
                            $data_cipher_type,
                            true,
                        );
                    }

                }else{

                    $parameters = EncodeClipper::customEncryptionDecryption(
                        $parameters,
                        $data_cipher_type,
                        true,
                    );

                }

            }catch (\Throwable $e){
				
                throw new Exception($e);
            }

            return $parameters;

        }
        public static function urlParameterEncoded($param){
            return enableUrlEncode() ? EncodeClipper::customEncryptionDecryption(
                $param,
                DECRYPTED_PARAM,
                true,
            ) : $param;
        }


        public static function safeUrl($value, $decode = false)
        {
            $replacements = [
                '/'  => '__SLASH__',
                '#'  => '__HASH__',
                '?'  => '__QUERY__',
                '&'  => '__AMP__',
                '='  => '__EQ__',
                '%'  => '__PERCENT__',
            ];
            if ($decode) {

                $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5);
                $value = self::rawurldecode($value);
                foreach (Arr::arrFlip($replacements) as $search => $replace) {
                    $value = Str::replace($search, $replace, $value);
                }
            } else {
                foreach ($replacements as $search => $replace) {
                    $value = Str::replace($search, $replace, $value);
                }
                $value = self::rawurlencode($value);
            }

            return $value;
        }

        public static function rawurlencode($value)
        {
            return rawurlencode($value);
        }

        public static function rawurldecode($value)
        {
            return rawurldecode($value);
        }

	}
