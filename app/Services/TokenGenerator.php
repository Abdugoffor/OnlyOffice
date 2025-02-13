<?php

namespace App\Services;

class TokenGenerator
{
    protected static function base64UrlEncode($str)
    {
        return str_replace("/", "_", str_replace("+", "-", trim(base64_encode($str), "=")));
    }

    public static function encode($payload)
    {
        $header = [
            "alg" => "HS256",  // the hashing algorithm
            "typ" => "JWT"  // the token type
        ];
        $secret = config('services.onlyoffice.secret');
        
        $encHeader = self::base64UrlEncode(json_encode($header));  // header
        $encPayload = self::base64UrlEncode(json_encode($payload));  // payload
        $hash = self::base64UrlEncode(hash_hmac("sha256", "$encHeader.$encPayload", $secret, true));  // signature

        return "$encHeader.$encPayload.$hash";
    }
}
