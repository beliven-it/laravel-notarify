<?php

namespace Beliven\Notarify\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Beliven\Notarify\Entities\Notarization;
use Symfony\Component\HttpFoundation\File\File;
use Beliven\Notarify\Exceptions\NotarizationAuthException;
use Beliven\Notarify\Contracts\NotarizationServiceContract;
use Beliven\Notarify\Exceptions\NotarizationUploadException;
use Beliven\Notarify\Exceptions\NotarizationVerificationException;

class IuscriboService implements NotarizationServiceContract
{
    private string $endpoint;
    private string $username;
    private string $password;
    private string $company;
    private string $blockchain;
    private string $sendMethod;
    private string $hashAlgorithm;

    public function __construct()
    {
        $this->endpoint = config('notarify.services.iuscribo.endpoint');
        $this->username = config('notarify.services.iuscribo.username');
        $this->password = config('notarify.services.iuscribo.password');
        $this->company = config('notarify.services.iuscribo.company');
        $this->blockchain = config('notarify.services.iuscribo.settings.blockchain');
        $this->sendMethod = config('notarify.services.iuscribo.settings.send-method');
        $this->hashAlgorithm = config('notarify.services.iuscribo.settings.hash-algorithm');
    }

    public function upload(File $file): Notarization
    {
        $token = $this->getAuthToken();

        $fileName = $file->getClientOriginalName();
        $fileContent = $file->getContent();

        $response = Http::withToken($token)
            ->timeout(60)
            ->attach('File', $fileContent, $fileName)
            ->post("{$this->endpoint}/notarization/{$this->company}/createandfinalize", [
                'Description' => "Notarization of {$fileName}",
                'Notes' => "Notarization of {$fileName}",
                'SendMethod' => $this->sendMethod,
                'BlockChainName' => $this->blockchain,
            ]);

            $result = $response->json();

            $notarizationId = $result['value']['id'] ?? null;
            $notarizationHash = $result['value']['document']['documentHash'] ?? null;
            $notarizationBlockchainExplorer = $result['value']['blockchainExplorer'] ?? null;
            $notarizationTransactionId = $result['value']['coordinatesStep']['transactionId'] ?? null;
            $notarizationDate = isset($result['value']['notarizationDate'])
                ? Carbon::parse($result['value']['notarizationDate'])->setTimezone('UTC')
                : null;

            if (!$notarizationId || !$notarizationDate || !$notarizationHash || !$notarizationBlockchainExplorer || !$notarizationTransactionId) {
                $errorMessage = $result['errorSummary'] ?? null;
                throw new NotarizationUploadException($errorMessage);
            }

            return (new Notarization($notarizationId, $notarizationHash))
                ->setTimestamp($notarizationDate)
                ->addExplorerUrl("{$notarizationBlockchainExplorer}{$notarizationTransactionId}");
    }

    public function verify(File|Notarization $notarization): Notarization
    {
        $token = $this->getAuthToken();

        $hash = $notarization instanceof Notarization
            ? $notarization->getHash()
            : hash($this->hashAlgorithm, $notarization->getContent());


        $response = Http::withToken($token)->get("{$this->endpoint}/notarization/{$this->company}/infobyhashcode", [
            'hashcode' => $hash,
        ]);

        $result = $response->json();

        $notarizationId = $result['value']['id'] ?? null;
        $notarizationHash = $result['value']['document']['documentHash'] ?? null;
        $notarizationBlockchainExplorer = $result['value']['blockchainExplorer'] ?? null;
        $notarizationTransactionId = $result['value']['coordinatesStep']['transactionId'] ?? null;
        $notarizationDate = isset($result['value']['notarizationDate'])
        ? Carbon::parse($result['value']['notarizationDate'])->setTimezone('UTC')
        : null;

        if (!$notarizationId || !$notarizationHash || !$notarizationDate || !$notarizationTransactionId || !$notarizationBlockchainExplorer) {
            $errorMessage = $result['errorSummary'] ?? null;
            throw new NotarizationVerificationException($errorMessage);
        }

        return (new Notarization($notarizationId, $notarizationHash))
                ->setTimestamp($notarizationDate)
                ->addExplorerUrl("{$notarizationBlockchainExplorer}{$notarizationTransactionId}");
    }

    private function getAuthToken(): string
    {
        $response = Http::post("{$this->endpoint}/auth/login", [
            'UserName' => $this->username,
            'Password' => $this->password,
        ]);

        $result = $response->json();

        $token = $result['token'] ?? null;

        if (!$token) {
            $errorMessage = $result['errorSummary'] ?? null;
            throw new NotarizationAuthException($errorMessage);
        }

        return $token;
    }
}
