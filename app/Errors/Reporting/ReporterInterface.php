<?php

namespace App\Errors\Reporting;

use Throwable;

interface ReporterInterface
{
    /**
     * @param Throwable $throwable
     * @param array|null $metadata
     *
     * @return string|null
     */
    public function report(Throwable $throwable, ?array $metadata = null): ?string;
}
