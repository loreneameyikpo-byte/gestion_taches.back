<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_utilisateur_connecte_peut_se_deconnecter(): void
    {
        $user = User::factory()->create();

        $response = $this->fromFrontend()
            ->actingAs($user)
            ->postJson('/api/logout');

        $response->assertOk();
        $response->assertJsonPath('message', 'Déconnecté avec succès.');
        $this->assertGuest();
    }

    public function test_un_visiteur_non_connecte_ne_peut_pas_se_deconnecter(): void
    {
        $response = $this->fromFrontend()->postJson('/api/logout');

        $response->assertUnauthorized();
    }
}