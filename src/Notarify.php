<?php

namespace Beliven\Notarify;

use Beliven\Notarify\Contracts\NotarizationServiceContract;
use Beliven\Notarify\Entities\Notarization;
use Symfony\Component\HttpFoundation\File\File;

class Notarify
{
    public function __construct(private NotarizationServiceContract $notarizationService) {}

    public function upload(File $file): Notarization
    {
        return $this->notarizationService->upload($file);
    }

    public function verify(Notarization|File $notarization): Notarization
    {
        return $this->notarizationService->verify($notarization);
    }
}
