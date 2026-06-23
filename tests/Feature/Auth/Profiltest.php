<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_utilisateur_connecte_peut_consulter_son_profil(): void
    {
        $user = User::factory()->create(['name' => 'Ada Lovelace']);

        $response = $this->fromFrontend()
            ->actingAs($user)
            ->getJson('/api/me');

        $response->assertOk();
        $response->assertJsonPath('name', 'Ada Lovelace');
        $response->assertJsonPath('email', $user->email);
    }

    public function test_un_visiteur_non_connecte_ne_peut_pas_consulter_un_profil(): void
    {
        $response = $this->fromFrontend()->getJson('/api/me');

        $response->assertUnauthorized();
    }

    public function test_un_utilisateur_peut_modifier_son_nom_et_son_email(): void
    {
        $user = User::factory()->create();

        $response = $this->fromFrontend()
            ->actingAs($user)
            ->putJson('/api/profile', [
                'name' => 'Nouveau Nom',
                'email' => 'nouveau@example.com',
            ]);

        $response->assertOk();
        $response->assertJsonPath('name', 'Nouveau Nom');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'nouveau@example.com',
        ]);
    }

    public function test_un_utilisateur_peut_changer_son_mot_de_passe_avec_le_bon_mot_de_passe_actuel(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('ancien-mot-de-passe'),
        ]);

        $response = $this->fromFrontend()
            ->actingAs($user)
            ->putJson('/api/profile', [
                'current_password' => 'ancien-mot-de-passe',
                'password' => 'nouveau-mot-de-passe-123',
                'password_confirmation' => 'nouveau-mot-de-passe-123',
            ]);

        $response->assertOk();
        $this->assertTrue(Hash::check('nouveau-mot-de-passe-123', $user->refresh()->password));
    }

    public function test_le_changement_de_mot_de_passe_echoue_si_le_mot_de_passe_actuel_est_faux(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('ancien-mot-de-passe'),
        ]);

        $response = $this->fromFrontend()
            ->actingAs($user)
            ->putJson('/api/profile', [
                'current_password' => 'mauvais-mot-de-passe',
                'password' => 'nouveau-mot-de-passe-123',
                'password_confirmation' => 'nouveau-mot-de-passe-123',
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['current_password']);
    }

    public function test_la_modification_du_profil_refuse_un_email_deja_pris_par_un_autre_compte(): void
    {
        User::factory()->create(['email' => 'dejaprise@example.com']);
        $user = User::factory()->create();

        $response = $this->fromFrontend()
            ->actingAs($user)
            ->putJson('/api/profile', [
                'email' => 'dejaprise@example.com',
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['email']);
    }
}