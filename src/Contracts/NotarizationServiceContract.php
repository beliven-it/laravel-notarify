<?php

namespace Beliven\Notarify\Contracts;

use Beliven\Notarify\Entities\FileToNotarize;

interface NotarizationServiceContract
{
    public function upload(FileToNotarize $file);

    public function verify(FileToNotarize $file);
}
