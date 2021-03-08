<?php

namespace StepStone\PdfReactor\Data;

use stdClass;

/**
 * PDFreactor Version.
 * 
 * @link https://www.pdfreactor.com/product/doc/webservice/web-service-client.html#Version
 */
class Version extends AbstractData
{
    public $build;
    public $label;
    public $major;
    public $micro;
    public $minor;
    public $revision;

    protected function hydrate(stdClass $data)
    {
        $this->build    = optional($data)->build;
        $this->label    = optional($data)->label;
        $this->major    = optional($data)->major;
        $this->micro    = optional($data)->micro;
        $this->minor    = optional($data)->minor;
        $this->revision = optional($data)->revision;
    }
}