<?php

namespace Beliven\Notarify\Services;

use Illuminate\Support\Facades\Http;
use Beliven\Notarify\Entities\FileToNotarize;
use Beliven\Notarify\Exceptions\BlockchainException;
use Beliven\Notarify\Contracts\NotarizationServiceContract;

class IuscriboService implements NotarizationServiceContract
{
    public function upload(FileToNotarize $file)
    {
        throw new \Exception('Upload for Iuscribo not implemented');
    }

    public function verify(FileToNotarize $file)
    {
        throw new \Exception('Verify for Iuscribo not implemented');
    }
}
