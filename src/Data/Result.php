<?php

namespace StepStone\PdfReactor\Data;

use stdClass;

/**
 * Result Object.
 * 
 * @link https://www.pdfreactor.com/product/doc/webservice/web-service-client.html#Result
 */
class Result extends AbstractData
{
    /** @var string|null */
    public $callbackUrl;

    /** @var string|null */
    public $connections;

    /** @var string|null */
    public $contentType;

    /** @var string|null */
    public $conversionName;

    /** @var string|null */
    public $document;

    /** @var array */
    public $documentArray;

    /** @var string|null */
    public $documentId;

    /** @var string|null */
    public $documentUrl;

    /** @var string|null */
    public $endDate;

    /** @var string|null */
    public $error;

    /** @var array */
    public $exceedingContents;

    /** @var string|null */
    public $javaScriptExports;

    /** @var bool */
    public $keepDocument;

    /** @var Log */
    public $log;

    /** @var int */
    public $numberOfPages           = 0;

    /** @var int */
    public $numberOfPagesLiteral    = 0;

    /** @var string */
    public $startDate;

    protected function hydrate(stdClass $data)
    {
        $this->callbackUrl          = optional($data)->callbackUrl;
        $this->connections          = optional($data)->connections;
        $this->contentType          = optional($data)->contentType;
        $this->conversionName       = optional($data)->conversionName;
        $this->document             = optional($data)->document;
        $this->documentArray        = optional($data)->documentArray ?: [];
        $this->documentId           = optional($data)->documentId;
        $this->documentUrl          = optional($data)->documentUrl;
        $this->endDate              = optional($data)->endDate;
        $this->error                = optional($data)->error;
        $this->exceedingContents    = optional($data)->exceedingContents ?: [];
        $this->javaScriptExports    = optional($data)->javaScriptExports;
        $this->keepDocument         = (bool)optional($data)->keepDocument;
        $this->log                  = isset($data->log) ? Log::make($data->log) : null;
        $this->numberOfPages        = optional($data)->numberOfPages ?: 0;
        $this->numberOfPagesLiteral = optional($data)->numberOfPagesLiteral ?: 0;
        $this->startDate            = optional($data)->startDate;
    }
}