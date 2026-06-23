<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    prtected function fromFrontend(

    ): static{
        return $this->withHeaders(
            'referer', 'http://localhost:3000'
        );
    }
}
