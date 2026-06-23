<?php
namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_users_can_register()
    {
        $response = $this->fromFrontend()->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertCreated();
        $response->assertJsonStructure([
            /*'message',*/
                //'user' =>/* [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
        ]);
        
        $response->assertJsonPath('name', 'John Doe');
        $response->assertJsonPath('email', 'john@example.com');

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
        $this->assertAuthenticated();
    }
    
    public function test_le_mot_de_passe_est_hache_en_base(): void
    {
        $this->fromFrontend()->postJson('/api/register', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'password' => 'mot-de-passe-123',
            'password_confirmation' => 'mot-de-passe-123',
        ]);
 
        $user = User::where('email', 'ada@example.com')->first();
 
        $this->assertNotSame('mot-de-passe-123', $user->password);
    }
 
    public function test_inscription_refusee_si_des_champs_obligatoires_manquent(): void
    {
        $response = $this->fromFrontend()->postJson('/api/register', []);
 
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }
 
    public function test_inscription_refusee_si_email_deja_utilise(): void
    {
        User::factory()->create(['email' => 'ada@example.com']);
 
        $response = $this->fromFrontend()->postJson('/api/register', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'password' => 'mot-de-passe-123',
            'password_confirmation' => 'mot-de-passe-123',
        ]);
 
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['email']);
    }
 
    public function test_inscription_refusee_si_les_mots_de_passe_ne_correspondent_pas(): void
    {
        $response = $this->fromFrontend()->postJson('/api/register', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'password' => 'mot-de-passe-123',
            'password_confirmation' => 'autre-chose',
        ]);
 
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['password']);
    }
}
