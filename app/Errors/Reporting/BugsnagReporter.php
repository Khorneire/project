<?php

namespace App\Errors\Reporting;

use App\Errors\Exceptions\ApplicationException;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Throwable;

class BugsnagReporter implements ReporterInterface
{
    /**
     * @param Throwable $throwable
     * @param array|null $metadata
     *
     * @return string|null
     */
    public function report(Throwable $throwable, ?array $metadata = null): ?string
    {
        $identifier = uniqid();

        Bugsnag::registerCallback(function ($report) use ($metadata, $throwable, $identifier) {

            $report->setMetaData([
                'error' => [
                    'public' => $identifier,
                    'internal' => ($throwable instanceof ApplicationException) ? $throwable->getApplicationCode() : null
                ]
            ]);

            if (!empty($metadata)) {
                $report->setMetaData(['metadata' => $metadata]);
            }

            if ($throwable instanceof ApplicationException && $throwable->hasMetaData()) {
                $report->setMetaData(['metadata' => $throwable->getMetaData()]);
            }

            if ($throwable->getPrevious() !== null) {
                $report->setMetaData([
                    'previous' => [
                        'message' => $throwable->getPrevious()->getMessage(),
                        'code' => $throwable->getPrevious()->getCode(),
                        'line' => $throwable->getPrevious()->getLine(),
                        'file' => $throwable->getPrevious()->getFile(),
                        'stacktrace' => explode("\n", $throwable->getPrevious()->getTraceAsString())
                    ]
                ]);
            }
        });

        return $identifier;
    }
}
