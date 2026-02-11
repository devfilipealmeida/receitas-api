<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            'Bolos e tortas doces',
            'Carnes',
            'Aves',
            'Peixes e frutos do mar',
            'Saladas, molhos e acompanhamentos',
            'Sopas',
            'Massas',
            'Bebidas',
            'Doces e sobremesas',
            'Lanches',
            'Prato Único',
            'Light',
            'Alimentação Saudável',
        ];

        foreach ($categorias as $ordem => $nome) {
            DB::table('categorias')->insertOrIgnore([
                'id' => $ordem + 1,
                'nome' => $nome,
            ]);
        }
    }
}
