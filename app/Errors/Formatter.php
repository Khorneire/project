<?php

namespace App\Errors;

use Illuminate\Validation\ValidationException;
use App\Errors\Exceptions\ApplicationException;
use Throwable;

class Formatter
{
    /**
     * Returns the Exception message depending on the debug setting, and if it originated within our application code.
     * This helps prevent leaking potentially sensitive information or un-user-friendly messages.
     *
     * @param Throwable $exception
     *
     * @return string
     */
    public static function getMessage(Throwable $exception): string
    {
        $isApplicationException = ($exception instanceof ApplicationException);

        return (!$isApplicationException)
            ? 'Sorry, an unexpected error occurred.'
            : $exception->getMessage();
    }

    /**
     * Returns the Application Error code if applicable.
     *
     * @param Throwable $exception
     *
     * @return int|null
     */
    public static function getApplicationCode(Throwable $exception): ?int
    {
        return ($exception instanceof ApplicationException)
            ? $exception->getApplicationCode()
            : null;
    }

    /**
     * Returns the exception stack trace if applicable.
     *
     * @param Throwable $exception
     *
     * @return array|null
     */
    public static function getStackTrace(Throwable $exception): ?array
    {
        return explode("\n", $exception->getTraceAsString());
    }

    /**
     * Returns the failed validation fields, if applicable.
     *
     * @param Throwable $exception
     *
     * @return array|null
     */
    public static function getValidationFields(Throwable $exception): ?array
    {
        return ($exception instanceof ValidationException)
            ? $exception->errors()
            : null;
    }
}
