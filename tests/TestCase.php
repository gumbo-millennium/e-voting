<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;
    use HasFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->withoutMix();
    }
}
