<?php

namespace Tests\Feature\Api;

use App\Models\Receita;
use App\Models\User;
use Tests\TestCase;

class ReceitaApiTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['login' => 'user_receitas', 'senha' => 'senha123']);
    }

    public function test_store_returns_201_when_authenticated(): void
    {
        $payload = [
            'nome' => 'Bolo simples',
            'modo_preparo' => 'Misture tudo e asse.',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/receitas', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id', 'id_usuarios', 'id_categorias', 'categoria', 'nome', 'tempo_preparo_minutos',
                'porcoes', 'modo_preparo', 'ingredientes', 'criado_em', 'alterado_em',
            ])
            ->assertJson([
                'id_usuarios' => $this->user->id,
                'nome' => 'Bolo simples',
                'modo_preparo' => 'Misture tudo e asse.',
            ]);
        $this->assertDatabaseHas('receitas', [
            'id_usuarios' => $this->user->id,
            'nome' => 'Bolo simples',
        ]);
    }

    public function test_store_returns_401_when_unauthenticated(): void
    {
        $response = $this->postJson('/api/receitas', [
            'modo_preparo' => 'Modo.',
        ]);
        $response->assertStatus(401);
    }

    public function test_store_returns_422_when_modo_preparo_missing(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/receitas', []);
        $response->assertStatus(422)->assertJsonValidationErrors(['modo_preparo']);
    }

    public function test_index_returns_200_with_user_receitas(): void
    {
        Receita::create([
            'id_usuarios' => $this->user->id,
            'nome' => 'Receita A',
            'modo_preparo' => 'Modo A',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/receitas');

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonPath('data.0.nome', 'Receita A');
    }

    public function test_index_returns_401_when_unauthenticated(): void
    {
        $response = $this->getJson('/api/receitas');
        $response->assertStatus(401);
    }

    public function test_index_filters_by_nome(): void
    {
        Receita::create([
            'id_usuarios' => $this->user->id,
            'nome' => 'Bolo de chocolate',
            'modo_preparo' => 'Modo.',
        ]);
        Receita::create([
            'id_usuarios' => $this->user->id,
            'nome' => 'Sopa de legumes',
            'modo_preparo' => 'Modo.',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/receitas?nome=Bolo');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertSame('Bolo de chocolate', $data[0]['nome']);
    }

    public function test_show_returns_200_for_owner(): void
    {
        $receita = Receita::create([
            'id_usuarios' => $this->user->id,
            'nome' => 'Minha receita',
            'modo_preparo' => 'Modo.',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/receitas/' . $receita->id);

        $response->assertStatus(200)
            ->assertJson(['id' => $receita->id, 'nome' => 'Minha receita']);
    }

    public function test_show_returns_404_when_not_owner(): void
    {
        $other = User::factory()->create();
        $receita = Receita::create([
            'id_usuarios' => $other->id,
            'nome' => 'Outra receita',
            'modo_preparo' => 'Modo.',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/receitas/' . $receita->id);

        $response->assertStatus(404);
    }

    public function test_show_returns_401_when_unauthenticated(): void
    {
        $receita = Receita::create([
            'id_usuarios' => $this->user->id,
            'nome' => 'R',
            'modo_preparo' => 'M',
        ]);
        $response = $this->getJson('/api/receitas/' . $receita->id);
        $response->assertStatus(401);
    }

    public function test_update_returns_200_for_owner(): void
    {
        $receita = Receita::create([
            'id_usuarios' => $this->user->id,
            'nome' => 'Antes',
            'modo_preparo' => 'Modo.',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/receitas/' . $receita->id, [
                'nome' => 'Depois',
                'modo_preparo' => 'Modo.',
            ]);

        $response->assertStatus(200)->assertJson(['nome' => 'Depois']);
        $receita->refresh();
        $this->assertSame('Depois', $receita->nome);
    }

    public function test_update_returns_404_when_not_owner(): void
    {
        $other = User::factory()->create();
        $receita = Receita::create([
            'id_usuarios' => $other->id,
            'nome' => 'Outra',
            'modo_preparo' => 'Modo.',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/receitas/' . $receita->id, ['nome' => 'Hack', 'modo_preparo' => 'Modo.']);

        $response->assertStatus(404);
        $receita->refresh();
        $this->assertSame('Outra', $receita->nome);
    }

    public function test_destroy_returns_200_for_owner(): void
    {
        $receita = Receita::create([
            'id_usuarios' => $this->user->id,
            'nome' => 'Apagar',
            'modo_preparo' => 'Modo.',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson('/api/receitas/' . $receita->id);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Receita excluída com sucesso.']);
        $this->assertDatabaseMissing('receitas', ['id' => $receita->id]);
    }

    public function test_destroy_returns_404_when_not_owner(): void
    {
        $other = User::factory()->create();
        $receita = Receita::create([
            'id_usuarios' => $other->id,
            'nome' => 'Outra',
            'modo_preparo' => 'Modo.',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson('/api/receitas/' . $receita->id);

        $response->assertStatus(404);
        $this->assertDatabaseHas('receitas', ['id' => $receita->id]);
    }

    public function test_destroy_returns_401_when_unauthenticated(): void
    {
        $receita = Receita::create([
            'id_usuarios' => $this->user->id,
            'nome' => 'R',
            'modo_preparo' => 'M',
        ]);
        $response = $this->deleteJson('/api/receitas/' . $receita->id);
        $response->assertStatus(401);
    }
}
