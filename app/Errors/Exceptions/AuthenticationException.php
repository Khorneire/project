<?php

namespace App\Errors\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class AuthenticationException extends ApplicationException implements HttpExceptionInterface
{
    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return 401;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return [];
    }
}
