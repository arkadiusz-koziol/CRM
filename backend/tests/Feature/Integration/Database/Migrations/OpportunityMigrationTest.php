<?php

namespace Tests\Feature\Integration\Database\Migrations;

use Tests\TestCase;

class OpportunityMigrationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
