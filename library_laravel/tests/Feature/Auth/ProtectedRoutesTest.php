<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class ProtectedRoutesTest extends TestCase
{
    public function test_guest_is_redirected_from_protected_route(): void
    {
        $response = $this->get('/books');

        $response->assertRedirect('/login');
    }
}
