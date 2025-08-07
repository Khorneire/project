<?php

declare(strict_types=1);

namespace App\Errors\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class PageNotFoundException extends ApplicationException implements HttpExceptionInterface
{
    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return 404;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return [];
    }
}
