<?php

namespace Beliven\Notarify\Services;

use Beliven\Notarify\Contracts\NotarizationServiceContract;
use Beliven\Notarify\Entities\FileToNotarize;

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
