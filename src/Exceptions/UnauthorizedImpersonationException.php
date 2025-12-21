<?php

namespace Laravilt\Users\Exceptions;

use Exception;

class UnauthorizedImpersonationException extends Exception
{
    public function __construct(string $message = 'You are not authorized to impersonate users.')
    {
        parent::__construct($message);
    }
}
