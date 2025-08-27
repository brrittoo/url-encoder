<?php

    namespace Brrittoo\UrlEncoder\Utilities;

	use PHPUnit\Util\Exception;
	
	class Url
	{
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
		

	}
