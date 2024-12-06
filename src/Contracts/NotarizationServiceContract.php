<?php

namespace Beliven\Notarify\Contracts;

use Beliven\Notarify\Entities\Notarization;
use Symfony\Component\HttpFoundation\File\File;

interface NotarizationServiceContract
{
    public function upload(File $file): Notarization;

    public function verify(Notarization|File $notarization): Notarization;
}
