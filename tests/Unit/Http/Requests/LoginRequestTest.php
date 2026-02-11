<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    protected function rules(): array
    {
        $request = LoginRequest::createFrom(Request::create('/', 'POST', []))
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
            'login' => 'maria',
            'senha' => 'senha123',
        ];
        $validator = $this->validate($data);
        $this->assertFalse($validator->fails());
    }

    public function test_login_is_required(): void
    {
        $data = ['senha' => 'senha123'];
        $validator = $this->validate($data);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('login', $validator->errors()->toArray());
    }

    public function test_senha_is_required(): void
    {
        $data = ['login' => 'maria'];
        $validator = $this->validate($data);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('senha', $validator->errors()->toArray());
    }
}
