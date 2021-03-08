<?php

namespace StepStone\PdfReactor\Data;

use stdClass;

/**
 * An Object representing the progress of an asynchronous conversion.
 * 
 * @see https://www.pdfreactor.com/product/doc/webservice/web-service-client.html#Progress
 */
class Progress extends AbstractData
{
    /** @var string|null */
    public $callbackUrl;

    /** @var string|null */
    public $contentType;

    /** @var string|null */
    public $conversionName;

    /** @var string|null */
    public $document;

    /** @var string|null */
    public $documentId;

    /** @var string|null */
    public $documentUrl;

    /** @var string|null */
    public $finished;

    /** @var Log|null */
    public $log;

    /** @var int */
    public $progress;

    /** @var string|null */
    public $startDate;

    protected function hydrate(stdClass $data)
    {
        $this->callbackUrl      = optional($data)->callbackUrl;
        $this->contentType      = optional($data)->contentType;
        $this->conversionName   = optional($data)->conversionName;
        $this->document         = optional($data)->document;
        $this->documentId       = optional($data)->documentId;
        $this->documentUrl      = optional($data)->documentUrl;
        $this->finished         = optional($data)->finished;
        $this->log              = isset($data->log) ? Log::make($data->log) : null;
        $this->progress         = optional($data)->progress ?: 0;
        $this->startDate        = optional($data)->startDate;
    }
}