<?php

	namespace Utilities;

	class EncodeClipper
	{
        public static function customEncryptionDecryption($value, $action,  $isURL = false)
        {
            $secret_key = config('url-encoder.encryption_secret_key');
            $iv = config('url-encoder.encryption_fixed_iv');
            $salt = config('url-encoder.encryption_salt');

            if ($action == ENCRYPTED_PARAM) {

                if (is_array($value)){
                    $value = implode('|', $value);
                }
                $value = self::encrypt_custom($value, $secret_key, $iv, $salt);
                if ($isURL){

                    $value = Url::safeUrl($value);

                }

                return $value;

            } elseif ($action == DECRYPTED_PARAM) {

                if ($isURL){

                    $value = Url::safeUrl($value, true);

                }

                return self::decrypt_custom($value, $secret_key);
            }
            return $value;
        }

        public static function  encrypt_custom($data, $password, $iv = null, $salt = null){
            try{
                if (empty($data)){
                    return $data;
                }
                if (empty($iv)){
                    $iv = substr(sha1(mt_rand()), 0, 16);
                }

                $password = sha1($password);

                if (empty($salt)){
                    $salt = substr(sha1(mt_rand()), 0, 4);
                }

                $saltWithPassword = hash('sha256', $password.$salt);

                $encrypted = openssl_encrypt(
                    "$data", config('url-encoder.encryption_method') , "$saltWithPassword", 0, $iv
                );
                $msg_encrypted_bundle = "$iv:$salt:$encrypted";
                return $msg_encrypted_bundle;
            }catch (\Exception $e){
                return $data;
            }
        }

        public static function decrypt_custom($msg_encrypted_bundle, $password)
        {
            try{
                if (empty($msg_encrypted_bundle)){
                    return $msg_encrypted_bundle;
                }
                $password = sha1($password);

                $components = explode( ':', $msg_encrypted_bundle );

                if(count($components) < 3){
                    return $msg_encrypted_bundle;
                }

                $iv            = $components[0] ?? '';

                $salt = $components[1] ?? '';
                $salt          = hash('sha256', $password.$salt);
                $encrypted_msg = $components[2] ?? '';

                $decrypted_msg = openssl_decrypt(
                    $encrypted_msg, config('url-encoder.encryption_method'), $salt, 0, $iv
                );

                if ( $decrypted_msg === false ){
                    return $msg_encrypted_bundle;
                }

                //$msg = substr( $decrypted_msg, 41 );
                return (string) $decrypted_msg;
            }catch(\Exception $e){
                return $msg_encrypted_bundle;
            }
        }
	}
