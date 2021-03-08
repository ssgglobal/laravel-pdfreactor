<?php

namespace StepStone\PdfReactor\Data;

use Exception;
use stdClass;

abstract class AbstractData
{
    /**
     * Class Constructor.
     *
     * @param string|stdClass $data
     */
    public function __construct($data)
    {
        if (is_string($data)) {
            $data   = json_decode($data);
        }

        if (! $data instanceof stdClass) {
            throw new Exception("Failed to decode data.");
        }

        $this->hydrate($data);
    }

    /**
     * Hydrate the class with json data.
     *
     * @param string $data
     * @return void
     */
    abstract protected function hydrate(stdClass $data);

    /**
     * Create a new Data Object.
     *
     * @param string|stdClass $data
     * @return self
     */
    public static function make($data): self
    {
        return (new static($data));
    }
}