<?php

namespace Tests\Feature\Unit\Domain\Crm\Entity;

use Tests\TestCase;

class PipelineTest extends TestCase
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
