<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\StoreReceitaRequest;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreReceitaRequestTest extends TestCase
{
    protected function rules(): array
    {
        $request = StoreReceitaRequest::createFrom(Request::create('/', 'POST', []))
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
            'modo_preparo' => 'Misture tudo e asse por 40 min.',
        ];
        $this->assertFalse($this->validate($data)->fails());
    }

    public function test_valid_data_with_all_fields_passes(): void
    {
        Categoria::create(['nome' => 'Bolos']);
        $data = [
            'id_categorias' => 1,
            'nome' => 'Bolo de chocolate',
            'tempo_preparo_minutos' => 45,
            'porcoes' => 8,
            'modo_preparo' => 'Misture os ingredientes.',
            'ingredientes' => 'Farinha, ovos.',
        ];
        $this->assertFalse($this->validate($data)->fails());
    }

    public function test_modo_preparo_is_required(): void
    {
        $validator = $this->validate([]);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('modo_preparo', $validator->errors()->toArray());
    }

    public function test_nome_max_45_characters(): void
    {
        $data = [
            'nome' => str_repeat('a', 46),
            'modo_preparo' => 'Modo.',
        ];
        $validator = $this->validate($data);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('nome', $validator->errors()->toArray());
    }

    public function test_id_categorias_must_exist(): void
    {
        $data = [
            'id_categorias' => 999,
            'modo_preparo' => 'Modo.',
        ];
        $validator = $this->validate($data);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('id_categorias', $validator->errors()->toArray());
    }

    public function test_tempo_preparo_minutos_and_porcoes_min_zero(): void
    {
        $data = [
            'tempo_preparo_minutos' => -1,
            'porcoes' => -1,
            'modo_preparo' => 'Modo.',
        ];
        $validator = $this->validate($data);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('tempo_preparo_minutos', $validator->errors()->toArray());
        $this->assertArrayHasKey('porcoes', $validator->errors()->toArray());
    }
}
