<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Testing\Assert;
use Illuminate\Testing\TestResponse;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        TestResponse::macro('assertSessionHasFlash', function ($level = '', $contains = '') {
            $this->assertSessionHas('flash_notification');

            $test = Session::get('flash_notification')->some(function ($notification) use ($level, $contains) {
                return $notification->level === $level
                    && Str::contains($notification->message, $contains);
            });

            if (! $test) {
                Assert::fail("No Flash message with `{$level}` level and containing `{$contains}`.");
            }
        });

        return $app;
    }
}
