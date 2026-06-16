<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Nonaktifkan Vite selama testing agar tidak error manifest missing
        if (method_exists($this, 'withoutVite')) {
            $this->withoutVite();
        }

        // Nonaktifkan proteksi CSRF selama testing agar request POST/PUT/DELETE lancar
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
    }
}
