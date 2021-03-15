<?php

namespace StepStone\PdfReactor\Tests\Unit;

use PdfReactor;
use StepStone\PdfReactor\Convertable;
use StepStone\PdfReactor\Data\Progress;
use StepStone\PdfReactor\Data\Result;
use StepStone\PdfReactor\Data\Version;
use StepStone\PdfReactor\Tests\TestCase;

class PdfReactorTest extends TestCase
{
    public function test_init_api()
    {
        $this->assertInstanceOf(\StepStone\PdfReactor\PdfReactor::class, PdfReactor::getPdfReactor());
    }

    public function test_get_reactor_version()
    {
        $version    = PdfReactor::getVersion();

        $this->assertInstanceOf(Version::class, $version);
        $this->assertIsInt($version->build);
        $this->assertIsInt($version->major);
        $this->assertSame((int)getenv('PDFREACTOR_VERSION'), $version->major);
    }

    public function test_convert_async()
    {
        $config         = new Convertable('<strong>Test</strong>');
        $documentId     = PdfReactor::convertAsync($config);

        $this->assertIsString($documentId);
        $this->assertInstanceOf(Progress::class, PdfReactor::getProgress($documentId));

        // sleep until we have a completed document.
        do {
            sleep(2);
            $documentProgress   = PdfReactor::getProgress($documentId);

        } while ($documentProgress->finished == false);

        // attempt to get the document
        $result = PdfReactor::getDocument($documentId);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertIsString($result->document);
    }
}