<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $database = config('database.connections.mysql.database');
        if ($database !== 'db_kapuas_test') {
            throw new RuntimeException("CRITICAL: Pengujian otomatis harus berjalan pada database 'db_kapuas_test'. Koneksi saat ini terhubung ke '{$database}'. Eksekusi dihentikan.");
        }
    }
}
