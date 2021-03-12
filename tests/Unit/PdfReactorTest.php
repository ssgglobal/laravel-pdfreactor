<?php

namespace StepStone\PdfReactor\Tests\Unit;

use StepStone\PdfReactor\Convertable;
use StepStone\PdfReactor\Data\Progress;
use StepStone\PdfReactor\Data\Result;
use StepStone\PdfReactor\Data\Version;
use StepStone\PdfReactor\PdfReactor;
use StepStone\PdfReactor\Tests\TestCase;

class PdfReactorTest extends TestCase
{
    public function test_init_api()
    {
        $this->assertInstanceOf(PdfReactor::class, $this->pdfReactor);
    }

    public function test_get_reactor_version()
    {
        $version    = $this->pdfReactor->getVersion();

        $this->assertInstanceOf(Version::class, $version);
        $this->assertIsInt($version->build);
        $this->assertIsInt($version->major);
        $this->assertSame((int)getenv('PDFREACTOR_VERSION'), $version->major);
    }

    public function test_convert_async()
    {
        $config         = new Convertable('<strong>Test</strong>');
        $documentId     = $this->pdfReactor->convertAsync($config);

        $this->assertIsString($documentId);
        $this->assertInstanceOf(Progress::class, $this->pdfReactor->getProgress($documentId));

        // sleep until we have a completed document.
        do {
            sleep(2);
            $documentProgress   = $this->pdfReactor->getProgress($documentId);

        } while ($documentProgress->finished == false);

        // attempt to get the document
        $result = $this->pdfReactor->getDocument($documentId);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertIsString($result->document);
    }
}