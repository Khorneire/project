<?php

namespace App\Errors\Exceptions;

use App\Errors\Reasons;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class InsufficientPermissionsException extends ApplicationException implements HttpExceptionInterface
{
    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return 403;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return [];
    }

    /**
     * @param int $permission
     *
     * @return InsufficientPermissionsException
     */
    public static function forPermission(int $permission): self
    {
        return (isset(Reasons::PERMISSION_VIOLATIONS[$permission]))
            ? InsufficientPermissionsException::forReason(Reasons::PERMISSION_VIOLATIONS[$permission])
            : new InsufficientPermissionsException('You do not have permission to access this area.');
    }
}
