<?php

class Token
{
    public static function sign(array $payload, string $key, $expire = null)
    {
        $header = [
            'algo' => 'HS256',
            'type' => 'JWT'
        ];

        if ($expire) {
            $header['expire'] = time() + $expire;
        }

        $header_encoded = base64_encode(
            json_encode($header)
        );

        $payload_encoded = base64_encode(
            json_encode($payload)
        );

        $signature = base64_encode(
            hash_hmac('SHA256', $header_encoded . $payload_encoded, $key)
        );

        return "$header_encoded.$payload_encoded.$signature";
    }

    public static function verify(string $token, string $key)
    {
        $token_parts = explode('.', $token);

        if (!$token_parts[0] || !$token_parts[1] || !$token_parts[2]) {
            return false;
        }

        $signature = base64_encode(
            hash_hmac('SHA256', $token_parts[0] . $token_parts[1], $key)
        );

        if ($signature != $token_parts[2]) {
            return false;
        }

        $header = (array) json_decode(
            base64_decode($token_parts[0])
        );

        if (isset($header['expire'])) {
            if ($header['expire'] < time()) {
                return false;
            }
        }

        $payload = json_decode(
            base64_decode($token_parts[1], true)
        );

        return $payload;
    }
}
