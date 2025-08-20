<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Concerns\HasFixtures;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, HasFixtures;

    /**
     * The base path for the tests.
     *
     * @var string
     */
    protected $basePath = __DIR__;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->app = $this->createApplication();
    }
}
