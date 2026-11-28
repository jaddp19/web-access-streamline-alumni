<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // The landing page counts users in the alumni role, so the role
        // must exist even when there are no alumni yet.
        Role::create([
            'name' => 'alumni',
            'guard_name' => 'web',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
