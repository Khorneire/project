<?php

/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Errors;

use Exception;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Errors\Reporting\ReporterInterface;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * @var string|null
     */
    private $identifier = null;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Throwable $exception
     *
     * @throws Exception
     */
    public function report(Throwable $exception)
    {
        $this->identifier = null;

        if (env('APP_ENV') === 'production' && $this->container->has(ReporterInterface::class)) {
            /** @var ReporterInterface $reporter */
            $reporter = $this->container->get(ReporterInterface::class);
            $this->identifier = $reporter->report($exception);
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $exception
     *
     * @throws Throwable
     * @return Response
     */
    public function render($request, Throwable $exception)
    {
        $rendered = parent::render($request, $exception);
        $rendered->headers->set('Error-Code', Formatter::getApplicationCode($exception));
        $rendered->headers->set('Error-Identifier', $this->identifier);

        if ($request->wantsJson()) {
            return new JsonResponse([
                'error' => [
                    'message'     => Formatter::getMessage($exception),
                    'application' => Formatter::getApplicationCode($exception),
                    'http'        => $rendered->getStatusCode(),
                    'identifier'  => $this->identifier,
                    'fields'      => Formatter::getValidationFields($exception),
                    'stacktrace'  => Formatter::getStackTrace($exception)
                ],
                'data'  => null
            ], $rendered->getStatusCode(), $rendered->headers->all());
        }

        return $rendered;
    }
}
