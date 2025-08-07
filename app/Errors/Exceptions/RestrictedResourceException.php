<?php

namespace App\Errors\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class RestrictedResourceException extends ApplicationException implements HttpExceptionInterface
{
    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return [];
    }

    /**
     * We're not exposing the current user doesn't have access which indicates the resource exists,
     * instead disguise the resource as not found.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return 404;
    }

    /**
     * @param array $reason
     * @param string $type
     * @param string|int $identifier
     *
     * @throws InvalidArgumentException
     * @return RestrictedResourceException
     */
    public static function resource(array $reason, string $type, string|int $identifier): self
    {
        if (!is_int($identifier) && !is_string($identifier)) {
            throw new InvalidArgumentException(
                sprintf('A resource identifier must be numeric or a string, %s given.', gettype($identifier))
            );
        }

        return self::forReason($reason)->withMetaData([
            'resource_type'       => $type,
            'resource_identifier' => $identifier
        ]);
    }
}
