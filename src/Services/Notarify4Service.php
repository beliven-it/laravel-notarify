<?php

namespace Beliven\Notarify\Services;

use Illuminate\Support\Facades\Http;
use Beliven\Notarify\Entities\FileToNotarize;
use Beliven\Notarify\Exceptions\BlockchainAuthException;
use Beliven\Notarify\Contracts\NotarizationServiceContract;

class Notarify4Service implements NotarizationServiceContract
{
    public function upload(FileToNotarize $file)
    {
        throw new \Exception('Upload for Notarify4 not implemented');
    }

    public function verify(FileToNotarize $file)
    {
        throw new \Exception('Verify for Notarify4 not implemented');
    }

    // private function auth()
    // {
    //     $response = Http::post(config('notarify.services.notarify4.endpoint') . "user/login", [
    //         'email' => config('notarify.services.notarify4.username'),
    //         'password' => config('notarify.services.notarify4.password')
    //     ]);

    //     $result = $response->json();

    //     if ($response->failed()) {
    //         throw new BlockchainAuthException($result['Error']);
    //     }

    //     return $result;
    // }
}
