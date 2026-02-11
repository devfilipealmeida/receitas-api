<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReceitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_categorias' => ['nullable', 'integer', 'exists:categorias,id'],
            'nome' => ['nullable', 'string', 'max:45'],
            'tempo_preparo_minutos' => ['nullable', 'integer', 'min:0'],
            'porcoes' => ['nullable', 'integer', 'min:0'],
            'modo_preparo' => ['required', 'string'],
            'ingredientes' => ['nullable', 'string'],
        ];
    }
}
