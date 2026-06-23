<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_users_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->fromFrontend()->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $response->assertOk();
        $response->assertJsonPath('email', 'john@example.com');
        $this->assertAuthenticated();
    }

 
    public function test_connexion_refusee_avec_un_mauvais_mot_de_passe(): void
    {
        User::factory()->create([
            'email' => 'ada@example.com',
            'password' => Hash::make('mot-de-passe-123'),
        ]);
 
        $response = $this->fromFrontend()->postJson('/api/login', [
            'email' => 'ada@example.com',
            'password' => 'mauvais-mot-de-passe',
        ]);
 
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['email']);
        $this->assertGuest();
    }
 
    public function test_connexion_refusee_avec_un_email_inconnu(): void
    {
        $response = $this->fromFrontend()->postJson('/api/login', [
            'email' => 'inconnu@example.com',
            'password' => 'peu-importe',
        ]);
 
        $response->assertUnprocessable();
        $this->assertGuest();
    }
 
    public function test_connexion_refusee_si_des_champs_manquent(): void
    {
        $response = $this->fromFrontend()->postJson('/api/login', []);
 
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['email', 'password']);
    }
}