<?php

namespace Beliven\Notarify\Services;

use Beliven\Notarify\Contracts\NotarizationServiceContract;
use Beliven\Notarify\Entities\Notarization;
use Beliven\Notarify\Exceptions\NotarizationAuthException;
use Beliven\Notarify\Exceptions\NotarizationUploadException;
use Beliven\Notarify\Exceptions\NotarizationVerificationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\File\File;

class ScalingParrotsService implements NotarizationServiceContract
{
    private string $endpoint;

    private string $username;

    private string $password;

    private string $hashAlgorithm;

    public function __construct()
    {
        $this->endpoint = config('notarify.services.scalingparrots.endpoint');
        $this->username = config('notarify.services.scalingparrots.username');
        $this->password = config('notarify.services.scalingparrots.password');
        $this->hashAlgorithm = config('notarify.services.scalingparrots.settings.hash-algorithm');
    }

    public function upload(File $file): Notarization
    {
        $authToken = $this->getAuthToken();

        $fileContent = $file->getContent();
        $fileHash = hash($this->hashAlgorithm, $fileContent);

        $response = Http::withToken($authToken)
            ->post("{$this->endpoint}/timestamp/stampHash", [
                'hash' => $fileHash,
                'hashAlgorithm' => $this->hashAlgorithm,
            ]);

        $result = $response->json();

        $notarizationId = $result[0]['txId'] ?? null;
        $notarizationHash = $result[0]['hash'] ?? null;
        $notarizationExplorerUrl = $result[0]['explorerUrl'] ?? null;

        if (! $notarizationHash || ! $notarizationId || ! $notarizationExplorerUrl) {
            $errorMessage = $result[0]['error'] ?? null;
            throw new NotarizationUploadException($errorMessage);
        }

        return (new Notarization($notarizationId, $notarizationHash))
            ->addExplorerUrl($notarizationExplorerUrl);
    }

    public function verify(Notarization|File $notarization): Notarization
    {
        $authToken = $this->getAuthToken();

        $hash = $notarization instanceof Notarization
            ? $notarization->getHash()
            : hash($this->hashAlgorithm, $notarization->getContent());

        $response = Http::withToken($authToken)
            ->get("{$this->endpoint}/timestamp/checkHash/?hash={$hash}");

        $result = $response->json();

        $notarizationId = $result[0]['txId'] ?? null;
        $notarizationHash = $result[0]['hash'] ?? null;
        $notarizationExplorerUrl = $result[0]['explorerUrl'] ?? null;
        $notarizationDate = isset($result[0]['timestamp'])
            ? Carbon::createFromTimestampUTC($result[0]['timestamp'])
            : null;

        if (! $notarizationId || ! $notarizationDate || ! $notarizationHash || ! $notarizationExplorerUrl) {
            $errorMessage = $result[0]['error'] ?? null;
            throw new NotarizationVerificationException($errorMessage);
        }

        return (new Notarization($notarizationId, $notarizationHash))
            ->setTimestamp($notarizationDate)
            ->addExplorerUrl($notarizationExplorerUrl);
    }

    private function getAuthToken()
    {
        $response = Http::post("{$this->endpoint}/user/login", [
            'Email' => $this->username,
            'Password' => $this->password,
        ]);

        $result = $response->json();

        $token = $result['token'] ?? null;

        if (! $token) {
            $errorMessage = $result[0]['error'] ?? null;
            throw new NotarizationAuthException($errorMessage);
        }

        return $token;
    }
}
