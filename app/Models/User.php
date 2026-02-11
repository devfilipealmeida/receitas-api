<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'usuarios';

    public const CREATED_AT = 'criado_em';
    public const UPDATED_AT = 'alterado_em';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nome',
        'login',
        'senha',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'senha',
    ];

    public function getAuthPasswordName(): string
    {
        return 'senha';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'senha' => 'hashed',
            'criado_em' => 'datetime',
            'alterado_em' => 'datetime',
        ];
    }

    public function receitas(): HasMany
    {
        return $this->hasMany(Receita::class, 'id_usuarios');
    }
}
