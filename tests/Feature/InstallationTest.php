<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstallationTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_redirect_to_installation_page()
    {
        $response = $this->get('/');

        $nbUsers = User::count();

        $this->assertEquals(0, $nbUsers);

        $response->assertRedirect(route('installation'));
    }
}
