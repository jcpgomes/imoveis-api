<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_com_credenciais_validas_retorna_token(): void
    {
        $user = User::factory()->create(['password' => 'senha-123']);

        $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'senha-123',
        ])->assertOk()->assertJsonStructure(['token', 'nome']);
    }

    public function test_login_com_senha_errada_retorna_401(): void
    {
        $user = User::factory()->create(['password' => 'senha-123']);

        $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'senha-errada',
        ])->assertUnauthorized();
    }

    public function test_login_com_email_mal_formatado_retorna_422(): void
    {
        $this->postJson('/api/login', [
            'email'    => 'email-invalido',
            'password' => 'senha-123',
        ])->assertUnprocessable()->assertJsonValidationErrors(['email']);
    }

    public function test_login_com_email_inexistente_retorna_erro(): void
    {
        $this->postJson('/api/login', [
            'email'    => 'naoexiste@exemplo.com',
            'password' => 'senha-123',
        ])->assertUnauthorized()
        ->assertJson(['message' => 'Credenciais inválidas.']);
    }

    public function test_rota_protegida_sem_token_retorna_erro(): void
    {
        $this->postJson('/api/properties')->assertUnauthorized();
    }

    public function test_logout_retorna_sucesso(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)->postJson('/api/logout')->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_usar_token_depois_do_logout_retorna_erro(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)->postJson('/api/logout')->assertOk();

        $this->app['auth']->forgetGuards();

        $this->withToken($token)->postJson('/api/properties')->assertUnauthorized();
    }

    
}