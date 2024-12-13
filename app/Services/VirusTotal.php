<?php

namespace App\Services;

use App\Entities\File;
use App\Enums\SecurityStatus;
use App\Interfaces\Antivirus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class VirusTotal implements Antivirus
{
    private string $apiKey;
    private string $endPoint = "https://www.virustotal.com/api/v3/files";

    public function __construct()
    {
        $this->apiKey = config('services.virus_total.api_key');
    }

    public function getSecurityStatus(string $fileName, string $fileContent): SecurityStatus
    {
        $analysisUrl = $this->checkFile($fileName, $fileContent);
        $analysisResult = $this->getAnalysisByUrl($analysisUrl);

        $malicious = $analysisResult["data.attributes.stats.malicious"];
        $suspicious = $analysisResult["data.attributes.stats.suspicious"];

        return ($malicious > 0) ? SecurityStatus::malicious : (($malicious === 0 && $suspicious > 0) ? SecurityStatus::doubtful : SecurityStatus::safe);

    }

    private function checkFile(string $fileName, string $fileContent): string
    {
        $response = Http::attach(
            'file', $fileContent, $fileName
        )->withHeaders([
            'x-apikey' => $this->apiKey
        ])->post($this->endPoint);

        $response = collect(json_decode($response->body(), true))->dot();

        return $response["data.links.self"];
    }

    private function getAnalysisByUrl(string $analysisUrl): Collection
    {
        $response = Http::withHeaders([
            'x-apikey' => $this->apiKey
        ])->get($analysisUrl);

        return collect(json_decode($response->body(), true))->dot();
    }
}
