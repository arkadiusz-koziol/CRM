<?php

namespace Tests\Feature\Integration\Database\Seeders;

use Tests\TestCase;

class PipelineSeederTest extends TestCase
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
