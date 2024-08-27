<?php

namespace App\Services;

use App\Entities\File;
use App\Enums\SecurityStatus;
use App\Interfaces\Antivirus;
use Illuminate\Support\Facades\Http;

class VirusTotal implements Antivirus
{
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = env('VIRUS_TOTAL_API_KEY');
    }

    public function getSecurityStatus(string $fileName, string $content): SecurityStatus
    {
        $resultUrl = $this->check($fileName, $content);
        dump($resultUrl);
        $result = $this->getAnalysisById($resultUrl);
        dump($result->json()["data"]["attributes"]["stats"]);
        $malicious = $result->json()["data"]["attributes"]["stats"]["malicious"];
        $suspicious = $result->json()["data"]["attributes"]["stats"]["suspicious"];
        dump($malicious);
        dump($suspicious);
        if ($malicious === 0 && $suspicious === 0) {
            return SecurityStatus::safe;
        } elseif ($malicious === 0 && $suspicious !== 0) {
            return SecurityStatus::doubtful;
        } else {
            return SecurityStatus::malicious;
        }
//        return SecurityStatus::safe;
    }

    public function check(string $name, string $content)
    {
        $response = Http::attach(
            'file', $content, $name
        )->withHeaders([
            'x-apikey' => $this->apiKey
        ])->post('https://www.virustotal.com/api/v3/files');
       $result = json_decode($response->body(), true);
       $url = $result["data"]["links"]["self"];
       return $url;
    }

    private function getAnalysisById(string $url)
    {
        return $response = Http::withHeaders([
            'x-apikey' => $this->apiKey
        ])->get($url);
    }
}
