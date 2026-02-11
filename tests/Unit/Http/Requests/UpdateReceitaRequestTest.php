<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\UpdateReceitaRequest;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateReceitaRequestTest extends TestCase
{
    protected function rules(): array
    {
        $request = UpdateReceitaRequest::createFrom(Request::create('/', 'PUT', []))
            ->setContainer($this->app);
        return $request->rules();
    }

    protected function validate(array $data): \Illuminate\Validation\Validator
    {
        return Validator::make($data, $this->rules());
    }

    public function test_empty_data_passes_validation(): void
    {
        $this->assertFalse($this->validate([])->fails());
    }

    public function test_modo_preparo_required_when_present(): void
    {
        $data = ['modo_preparo' => ''];
        $validator = $this->validate($data);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('modo_preparo', $validator->errors()->toArray());
    }

    public function test_nome_max_45_characters(): void
    {
        $data = [
            'nome' => str_repeat('a', 46),
        ];
        $validator = $this->validate($data);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nome', $validator->errors()->toArray());
    }

    public function test_id_categorias_must_exist(): void
    {
        $data = ['id_categorias' => 999];
        $validator = $this->validate($data);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('id_categorias', $validator->errors()->toArray());
    }
}
