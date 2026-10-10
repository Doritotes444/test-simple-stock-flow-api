<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class HealthCheckTest extends TestCase
{
    /**
     * P-01: Healthcheck 200 OK
     */
    public function test_p01_healthcheck_returns_200_ok(): void
    {
        $response = $this->get('/health');
        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);
    }
}