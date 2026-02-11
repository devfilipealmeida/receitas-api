<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\StoreUsuarioRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreUsuarioRequestTest extends TestCase
{
    protected function rules(): array
    {
        $request = StoreUsuarioRequest::createFrom(Request::create('/', 'POST', []))
            ->setContainer($this->app);

        return $request->rules();
    }

    protected function validate(array $data): \Illuminate\Validation\Validator
    {
        return Validator::make($data, $this->rules());
    }

    public function test_valid_data_passes_validation(): void
    {
        $data = [
            'nome' => 'Maria Silva',
            'login' => 'maria_silva',
            'senha' => 'senha123',
        ];
        $validator = $this->validate($data);
        $this->assertFalse($validator->fails());
    }

    public function test_nome_is_required(): void
    {
        $data = [
            'login' => 'maria',
            'senha' => 'senha123',
        ];
        $validator = $this->validate($data);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nome', $validator->errors()->toArray());
    }

    public function test_nome_max_100_characters(): void
    {
        $data = [
            'nome' => str_repeat('a', 101),
            'login' => 'maria',
            'senha' => 'senha123',
        ];
        $validator = $this->validate($data);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nome', $validator->errors()->toArray());
    }

    public function test_login_is_required(): void
    {
        $data = [
            'nome' => 'Maria',
            'senha' => 'senha123',
        ];
        $validator = $this->validate($data);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('login', $validator->errors()->toArray());
    }

    public function test_login_max_100_characters(): void
    {
        $data = [
            'nome' => 'Maria',
            'login' => str_repeat('a', 101),
            'senha' => 'senha123',
        ];
        $validator = $this->validate($data);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('login', $validator->errors()->toArray());
    }

    public function test_login_must_be_unique(): void
    {
        User::factory()->create(['login' => 'existente']);
        $data = [
            'nome' => 'Maria',
            'login' => 'existente',
            'senha' => 'senha123',
        ];
        $validator = $this->validate($data);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('login', $validator->errors()->toArray());
    }

    public function test_senha_is_required(): void
    {
        $data = [
            'nome' => 'Maria',
            'login' => 'maria',
        ];
        $validator = $this->validate($data);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('senha', $validator->errors()->toArray());
    }

    public function test_senha_min_6_characters(): void
    {
        $data = [
            'nome' => 'Maria',
            'login' => 'maria',
            'senha' => '12345',
        ];
        $validator = $this->validate($data);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('senha', $validator->errors()->toArray());
    }
}
