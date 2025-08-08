<?php

namespace App\Http;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Support\Responsable;

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
