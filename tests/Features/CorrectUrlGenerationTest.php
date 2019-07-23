<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Tests\Features;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Route;
use AvtoDev\RoadRunnerLaravel\Tests\AbstractTestCase;

/**
 * @coversNothing
 */
class CorrectUrlGenerationTest extends AbstractTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        \putenv('APP_URL=https://foo.bar.baz');

        parent::setUp();

        Route::get('/one/two/1/some', [
            'action' => function (): string {
                return 'boom!';
            },

            'as' => 'blah_name',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        \putenv('APP_URL'); // unset
    }

    /**
     * @return void
     */
    public function testColonMissedWhenUsedDefaultSchema(): void
    {
        /** @var UrlGenerator $url_generator */
        $url_generator = $this->app->make(UrlGenerator::class);

        $this->assertSame('https://foo.bar.baz/one/two/1/some', $url_generator->route('blah_name'));
    }
}
