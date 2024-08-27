<?php

namespace App\Services;

use App\Entities\File;
use App\Interfaces\Antivirus;
use Illuminate\Support\Facades\Http;

class VirusTotal implements Antivirus
{

    public function check(string $name, string $path)
    {
        $response = Http::attach(
            'file', file_get_contents($path), $name
        )->withHeaders([
            'x-apikey' => ''
        ])->post('https://www.virustotal.com/api/v3/files');

       $result = json_decode($response->body(), true);
       dump($result);
       $url = $result["data"]["links"]["self"];
       dump($url);
       $this->getAnalysisById($url);
    }

    private function getAnalysisById(string $url): void
    {
        $response = Http::withHeaders([
            'x-apikey' => ''
        ])->get($url);
        dump($response->json());
    }
}
