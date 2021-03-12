<?php

namespace StepStone\PdfReactor;

use Psr\Http\Message\ResponseInterface;

class ReactorResponse
{
    /** @var string|null */
    public $body;

    /** @var array */
    public $headers = [];

    /** @var int */
    public $status;

    /** @var bool */
    public $success;

    /**
     * Creates a digestable response message.
     *
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->body     = (string)$response->getBody();
        $this->headers  = $response->getHeaders();
        $this->status   = $response->getStatusCode();
        $this->success  = ($this->status >= 200 && $this->status <= 204);
    }
}