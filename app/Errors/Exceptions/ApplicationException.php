<?php

namespace App\Errors\Exceptions;

use Exception;
use Throwable;

/**
 * This is the top-level application exception, our exception hierarchy starts here.
 * Any Exceptions we throw within this application must extend this base Exception, either directly or in-directly.
 */
class ApplicationException extends Exception
{
    /**
     * Represents the internal application within the App\Errors\Reasons file.
     * We store this separately to the default $code property, to distinguish between application defined codes.
     *
     * @var int|null
     */
    protected ?int $applicationCode = null;

    /**
     * Any additional metadata which may be reported to error tracking systems.
     *
     * @var array
     */
    protected array $metadata = [];

    /**
     * @param array $reason
     * @param Throwable|null $previous
     * @param mixed ...$placeholders
     *
     * @return static
     */
    public static function forReason(array $reason, ?Throwable $previous = null, ...$placeholders): self
    {
        $exception = new static(
            sprintf($reason['message'], ...$placeholders),
            $reason['code'],
            $previous
        );

        $exception->applicationCode = $reason['code'];

        return $exception;
    }

    /**
     * Merges in any additional metadata to the Exception.
     *
     * @param array $metadata
     *
     * @return $this
     */
    public function withMetaData(array $metadata): self
    {
        $this->metadata = array_merge($this->metadata, $metadata);

        return $this;
    }

    /**
     * Returns the metadata associated with the Exception.
     *
     * @return array
     */
    public function getMetaData(): array
    {
        return $this->metadata;
    }

    /**
     * Determines if the Exception has any custom metadata.
     *
     * @return bool
     */
    public function hasMetaData(): bool
    {
        return (!empty($this->metadata));
    }

    /**
     * Returns the application specific error code, if applicable.
     *
     * @return int|null
     */
    public function getApplicationCode(): ?int
    {
        return $this->applicationCode;
    }
}
