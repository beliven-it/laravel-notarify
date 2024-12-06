<?php

namespace Beliven\Notarify\Services;

use Beliven\Notarify\Contracts\NotarizationServiceContract;
use Beliven\Notarify\Entities\FileToNotarize;
use Beliven\Notarify\Exceptions\BlockchainAuthException;
use Beliven\Notarify\Exceptions\BlockchainException;
use Illuminate\Support\Facades\Http;

class ScalingParrotsService implements NotarizationServiceContract
{
    public function upload(FileToNotarize $file)
    {
        $response = $this->auth();

        $response = Http::withToken($response['token'])->post(config('notarify.services.scaling_parrots.endpoint').'timestamp/stampHash', [
            'hash' => $file->hash,
            'hashAlgorithm' => 'sha256',
            'otherInfo' => $file->metaData,
        ]);

        $result = $response->json();

        if ($response->failed()) {
            throw new BlockchainException($result['Error']);
        }

        return $result[0];
    }

    public function verify(FileToNotarize $file)
    {
        $response = $this->auth();

        $response = Http::withToken($response['token'])->get(config('notarify.services.scaling_parrots.endpoint').'timestamp/checkHash/?hash='.$file->hash);

        $result = $response->json();

        if ($response->failed()) {
            throw new BlockchainException($result['Error']);
        }

        return $result[0];
    }

    private function auth()
    {
        $response = Http::post(config('notarify.services.scaling_parrots.endpoint').'user/login', [
            'Email' => config('notarify.services.scaling_parrots.username'),
            'Password' => config('notarify.services.scaling_parrots.password'),
        ]);

        $result = $response->json();

        if ($response->failed()) {
            throw new BlockchainAuthException($result['Error']);
        }

        return $result;
    }
}
