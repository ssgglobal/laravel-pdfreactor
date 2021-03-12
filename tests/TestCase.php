<?php

namespace StepStone\PdfReactor\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use StepStone\PdfReactor\PdfReactor;

class TestCase extends BaseTestCase
{
    /** @var PdfReactor */
    protected $pdfReactor;

    protected function setUp(): void
    {
        $this->pdfReactor     = new PdfReactor(getenv('PDFREACTOR_HOST'), getenv('PDFREACTOR_KEY'), getenv('PDFREACTOR_PORT'));
    }
}