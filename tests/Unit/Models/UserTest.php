<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_uses_usuarios_table(): void
    {
        $this->assertSame('usuarios', (new User)->getTable());
    }

    public function test_fillable_attributes(): void
    {
        $user = new User;
        $this->assertEquals(['nome', 'login', 'senha'], $user->getFillable());
    }

    public function test_senha_is_hidden_from_serialization(): void
    {
        $user = User::factory()->create();
        $array = $user->toArray();
        $this->assertArrayNotHasKey('senha', $array);
    }

    public function test_get_auth_password_name_returns_senha(): void
    {
        $user = new User;
        $this->assertSame('senha', $user->getAuthPasswordName());
    }

    public function test_senha_is_hashed_on_set(): void
    {
        $user = User::factory()->create(['senha' => 'plaintext']);
        $user->refresh();
        $this->assertNotSame('plaintext', $user->senha);
        $this->assertTrue(password_verify('plaintext', $user->senha));
    }

    public function test_uses_criado_em_and_alterado_em_as_timestamps(): void
    {
        $this->assertSame('criado_em', User::CREATED_AT);
        $this->assertSame('alterado_em', User::UPDATED_AT);
    }
}
