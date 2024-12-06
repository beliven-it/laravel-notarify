<?php

namespace Beliven\Notarify\Exceptions;

use Exception;

class BlockchainException extends Exception
{
    public function __construct($error)
    {
        parent::__construct($error, 500);
    }
}
