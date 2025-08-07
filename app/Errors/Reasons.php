<?php

namespace App\Errors;

class Reasons
{
    public const EXAMPLE_ERROR = [
        'code' => 1,
        'message' => 'This is an example error.'
    ];

    /**
     * Error Reasons specific to Permission failures.
     */
    public const PERMISSION_VIOLATIONS = [];
}
