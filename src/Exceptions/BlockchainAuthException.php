<?php

namespace Beliven\Notarify\Exceptions;

use Exception;

class BlockchainAuthException extends Exception
{
    public function __construct($error)
    {
        parent::__construct($error, 401);
    }
}
