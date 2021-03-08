<?php

namespace StepStone\PdfReactor\Exceptions;

use RuntimeException;
use Throwable;

class HttpException extends RuntimeException
{
    /**
     * HTTP/1.1 Status code.
     *
     * @var integer
     */
    protected $status;

    /**
     * Class Constructor.
     * 
     * Extend runtime exception by adding http status code.
     *
     * @param string $message
     * @param integer $status
     * @param integer $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message, int $status = 500, int $code = 0, ?Throwable $previous = null)
    {
        $this->status   = $status;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the HTTP Status code.
     *
     * @return integer
     */
    public function getStatus(): int
    {
        return $this->status;
    }
}