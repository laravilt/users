<?php

namespace Laravilt\Users\Exceptions;

use Exception;

class CannotImpersonateSelfException extends Exception
{
    public function __construct(string $message = 'You cannot impersonate yourself.')
    {
        parent::__construct($message);
    }
}
