<?php

namespace Laravilt\Users\Exceptions;

use Exception;

class CannotImpersonateSuperAdminException extends Exception
{
    public function __construct(string $message = 'You cannot impersonate a super admin.')
    {
        parent::__construct($message);
    }
}
