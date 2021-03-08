<?php

namespace StepStone\PdfReactor\Data;

use stdClass;

/**
 * An Object representing the log of a conversion.
 * 
 * https://www.pdfreactor.com/product/doc/webservice/web-service-client.html#Log
 */
class Log extends AbstractData
{
    /** @var array */
    protected $records;

    /** @var array */
    protected $recordsCss;

    /** @var array */
    protected $recordsJavaScript;

    protected function hydrate(stdClass $data)
    {
        $this->records              = (array)optional($data)->records ?: [];
        $this->recordsCss           = (array)optional($data)->recordsCss ?: [];
        $this->recordsJavaScript    = (array)optional($data)->recordsJavaScript ?: [];
    }
}