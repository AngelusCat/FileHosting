<?php

namespace App\Services;

use App\Entities\JWT;
use App\Exceptions\InvalidPayload;

class JWTAuth
{
    private string $header;
    private string $alg;
    private string $secret;

    public function __construct()
    {
        $this->header = json_encode([
            "alg" => "HS256",
            "typ" => "JWT"
        ]);
        $this->alg = json_decode($this->header, true)["alg"];
        $this->secret = env('JWT_SECRET');
    }

    /**
     * @throws InvalidPayload
     */
    public function createJWT(string $payload): JWT
    {
        if (str_contains($payload, "file_id") === false) {
            throw new InvalidPayload("Payload must contain the file_id field");
        }
        $headerBase64 = base64_encode($this->header);
        $payloadBase64 = base64_encode($payload);
        $headerBase64WithPayloadBase64 = $headerBase64 . "." . $payloadBase64;
        $signature = $this->createSignature($headerBase64WithPayloadBase64);
        $all = $headerBase64WithPayloadBase64 . "." . $signature;
        return new JWT($headerBase64, $payloadBase64, $signature, $all);
    }

    public function getJwtFromStringRepresentation(string $JwtInStringRepresentation): JWT
    {
        list($header, $payload, $signature) = preg_split("/\./", $JwtInStringRepresentation, -1, PREG_SPLIT_NO_EMPTY);
        return new JWT($header, $payload, $signature, $JwtInStringRepresentation);
    }

    private function createSignature(string $headerBase64WithPayloadBase64): string
    {
        if ($this->alg === "HS256") {
            return hash_hmac('sha256', $headerBase64WithPayloadBase64, $this->secret);
        } else {
            die;
        }
    }

    public function validateJWT(JWT $jwt): bool
    {
        $headerBase64 = $jwt->getHeaderBase64();
        $payloadBase64 = $jwt->getPayloadBase64();
        $headerBase64WithPayloadBase64 = $headerBase64 . "." . $payloadBase64;
        $signature = $this->createSignature($headerBase64WithPayloadBase64);
        return ($signature === $jwt->getSignature());
    }
}
