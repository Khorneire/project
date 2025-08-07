<?php

namespace App\Http;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Support\Responsable;
use App\Errors\Exceptions\InvalidArgumentException;

class Response implements Responsable
{
    /**
     * @var array|null
     */
    private ?array $data = null;

    /**
     * @var array|null
     */
    private ?array $pagination = null;

    /**
     * @var int
     */
    private int $status = 200;

    /**
     * @var array
     */
    private array $headers = [];

    /**
     * @param array|null $data
     * @param int $status
     * @param array $headers
     */
    public function __construct(?array $data = null, int $status = 200, array $headers = [])
    {
        $this->data = $data;
        $this->status = $status;
        $this->headers = $headers;
    }

    /**
     * @param NumericPaginationResult $pagination
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function withPagination(NumericPaginationResult $pagination): self
    {
        // We'll only support Numeric for now.
        // As we're unsure on bringing in Cursor based pagination, I won't create an Interface
        // for it now as it'll be presuming too much of the implementation.
        if (!$pagination instanceof NumericPaginationResult) {
            throw new InvalidArgumentException(sprintf(
                'Invalid pagination result provided for a response, expected %s, given %s.',
                NumericPaginationResult::class,
                is_object($pagination) ? get_class($pagination) : gettype($pagination)
            ));
        }

        $this->pagination = [
            'pages'  => [
                'current'  => $pagination->getCurrentPage(),
                'total'    => $pagination->getTotalPages(),
                'next'     => ($pagination->getCurrentPage() < $pagination->getTotalPages()),
                'previous' => ($pagination->getCurrentPage() > 1)
            ],
            'counts' => [
                'total' => $pagination->getTotalResults()
            ]
        ];

        return $this;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $data = ['data' => $this->data];

        if (!empty($this->pagination)) {
            $data['pagination'] = $this->pagination;
        }

        return new JsonResponse($data, $this->status, $this->headers);
    }
}
