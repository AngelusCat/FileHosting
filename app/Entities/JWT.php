<?php

namespace App\Entities;

class JWT
{
    private string $header = '{"typ":"JWT", "alg":"HS256"}';
    private string $secret;

    public function __construct()
    {
        $this->secret = env('JWT_SECRET');
    }

    public function create(string $payload): string
    {
        $header = base64_encode($this->header);
        $payload = base64_encode($payload);
        $signature = hash_hmac("sha256", $header . "." . $payload, $this->secret);
        return $header . "." . $payload . "." . $signature;
    }
}
