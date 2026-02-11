<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receita extends Model
{
    protected $table = 'receitas';

    public const CREATED_AT = 'criado_em';
    public const UPDATED_AT = 'alterado_em';

    protected $fillable = [
        'id_usuarios',
        'id_categorias',
        'nome',
        'tempo_preparo_minutos',
        'porcoes',
        'modo_preparo',
        'ingredientes',
    ];

    protected function casts(): array
    {
        return [
            'criado_em' => 'datetime',
            'alterado_em' => 'datetime',
            'tempo_preparo_minutos' => 'integer',
            'porcoes' => 'integer',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuarios');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'id_categorias');
    }
}
