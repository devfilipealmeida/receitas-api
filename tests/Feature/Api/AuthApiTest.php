<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    public function test_create_usuario_returns_201_and_json_with_user(): void
    {
        $payload = [
            'nome' => 'Maria Silva',
            'login' => 'maria_silva',
            'senha' => 'senha123',
        ];

        $response = $this->postJson('/api/usuarios', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'nome', 'login', 'criado_em'])
            ->assertJson([
                'nome' => 'Maria Silva',
                'login' => 'maria_silva',
            ])
            ->assertJsonMissing(['senha']);

        $this->assertDatabaseHas('usuarios', [
            'nome' => 'Maria Silva',
            'login' => 'maria_silva',
        ]);
        $user = User::first();
        $this->assertNotSame('senha123', $user->senha);
        $this->assertTrue(password_verify('senha123', $user->senha));
    }

    public function test_create_usuario_validation_fails_with_empty_body(): void
    {
        $response = $this->postJson('/api/usuarios', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome', 'login', 'senha']);
    }

    public function test_create_usuario_validation_fails_when_login_already_exists(): void
    {
        User::factory()->create(['login' => 'existente']);
        $payload = [
            'nome' => 'Outro',
            'login' => 'existente',
            'senha' => 'senha123',
        ];

        $response = $this->postJson('/api/usuarios', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['login']);
    }

    public function test_login_returns_200_with_token_and_usuario(): void
    {
        $user = User::factory()->create([
            'login' => 'maria',
            'senha' => 'senha123',
        ]);
        $payload = [
            'login' => 'maria',
            'senha' => 'senha123',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'usuario' => ['id', 'nome', 'login']])
            ->assertJson([
                'usuario' => [
                    'id' => $user->id,
                    'nome' => $user->nome,
                    'login' => 'maria',
                ],
            ]);
        $this->assertNotEmpty($response->json('token'));
    }

    public function test_login_returns_401_for_invalid_credentials(): void
    {
        User::factory()->create(['login' => 'maria']);
        $payload = [
            'login' => 'maria',
            'senha' => 'senha_errada',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Login ou senha inválidos.']);
    }

    public function test_login_validation_fails_when_login_or_senha_missing(): void
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['login', 'senha']);
    }

    public function test_logoff_returns_200_and_revokes_token(): void
    {
        $user = User::factory()->create(['login' => 'maria', 'senha' => 'senha123']);
        $token = $user->createToken('auth')->plainTextToken;

        $response = $this->postJson('/api/logoff', [], [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logoff realizado com sucesso.']);
        $this->assertCount(0, $user->tokens()->get());
    }

    public function test_logoff_returns_401_without_token(): void
    {
        $response = $this->postJson('/api/logoff');

        $response->assertStatus(401);
    }
}
