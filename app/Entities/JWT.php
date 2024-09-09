<?php

namespace App\Entities;

class JWT
{
    private string $headerBase64;
    private string $payloadBase64;
    private string $signature;
    private string $all;

    public function __construct(string $headerBase64, string $payloadBase64, string $signature, string $all)
    {
        $this->headerBase64 = $headerBase64;
        $this->payloadBase64 = $payloadBase64;
        $this->signature = $signature;
        $this->all = $all;
    }

    public function getSignature(): string
    {
        return $this->signature;
    }

    public function getHeaderBase64(): string
    {
        return $this->headerBase64;
    }

    public function getPayloadBase64(): string
    {
        return $this->payloadBase64;
    }

    public function getAll(): string
    {
        return $this->all;
    }
}
