<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $table = 'categorias';

    public $timestamps = false;

    protected $fillable = ['nome'];

    public function receitas(): HasMany
    {
        return $this->hasMany(Receita::class, 'id_categorias');
    }
}
