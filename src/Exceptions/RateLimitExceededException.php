<?php

namespace Sonysarkis\Skills\Exceptions;

use Exception;

class RateLimitExceededException extends Exception
{
    protected $message = 'Rate limit exceeded. Please try again later.';
}