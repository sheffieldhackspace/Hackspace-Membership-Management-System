<?php

namespace App\Exceptions;

use Exception;

class MustHavePrimaryEmailException extends Exception
{
    protected $message = 'A member must have a primary email address. set is_primary to true on another to override this one.';
}
