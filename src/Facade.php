<?php

namespace StepStone\PdfReactor;

use Illuminate\Support\Facades\Facade as BaseFacade;

class Facade extends BaseFacade
{
    protected static function getFacadeAccessor()
    {
        return 'StepStone\PdfReactor\PdfReactor';
    }
}