<?php

declare(strict_types=1);

namespace App\Exception;

class RetrospectiveNotActiveException extends \Exception
{
    /**
     * @param string          $message
     * @param int             $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = 'Retrospective is not active.', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
