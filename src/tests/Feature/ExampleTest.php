<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_returns_a_successful_redirect_response()
    {
        $response = $this->get('/');

        $response->assertStatus(302);
    }
}
