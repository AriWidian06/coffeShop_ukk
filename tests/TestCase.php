<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Aset Vite tidak tersedia pada job backend di CI (public/build di-gitignore
        // dan hanya dibangun oleh job frontend). Tanpa ini setiap test yang
        // merender view gagal 500 karena manifest.json tidak ditemukan.
        $this->withoutVite();
    }
}
