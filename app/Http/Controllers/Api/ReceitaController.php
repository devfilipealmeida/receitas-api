<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReceitaRequest;
use App\Http\Requests\UpdateReceitaRequest;
use App\Models\Receita;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReceitaController extends Controller
{
    public function store(StoreReceitaRequest $request): JsonResponse
    {
        $receita = $request->user()->receitas()->create($request->validated());

        return response()->json($this->receitaToArray($receita), 201);
    }

    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->receitas()->with('categoria:id,nome');

        if ($request->filled('categoria')) {
            $query->where('id_categorias', $request->integer('categoria'));
        }
        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . $request->string('nome') . '%');
        }

        $receitas = $query->orderByDesc('criado_em')->get();

        return response()->json([
            'data' => $receitas->map(fn (Receita $r) => $this->receitaToArray($r)),
        ]);
    }

    public function show(Request $request, Receita $receita): JsonResponse
    {
        if ($receita->id_usuarios !== $request->user()->id) {
            abort(404);
        }
        $receita->load('categoria:id,nome');

        return response()->json($this->receitaToArray($receita));
    }

    public function update(UpdateReceitaRequest $request, Receita $receita): JsonResponse
    {
        if ($receita->id_usuarios !== $request->user()->id) {
            abort(404);
        }
        $receita->update($request->validated());
        $receita->load('categoria:id,nome');

        return response()->json($this->receitaToArray($receita));
    }

    public function destroy(Request $request, Receita $receita): JsonResponse
    {
        if ($receita->id_usuarios !== $request->user()->id) {
            abort(404);
        }
        $receita->delete();

        return response()->json(['message' => 'Receita excluída com sucesso.']);
    }

    private function receitaToArray(Receita $receita): array
    {
        $receita->loadMissing('categoria:id,nome');
        return [
            'id' => $receita->id,
            'id_usuarios' => $receita->id_usuarios,
            'id_categorias' => $receita->id_categorias,
            'categoria' => $receita->categoria ? ['id' => $receita->categoria->id, 'nome' => $receita->categoria->nome] : null,
            'nome' => $receita->nome,
            'tempo_preparo_minutos' => $receita->tempo_preparo_minutos,
            'porcoes' => $receita->porcoes,
            'modo_preparo' => $receita->modo_preparo,
            'ingredientes' => $receita->ingredientes,
            'criado_em' => $receita->criado_em?->format('Y-m-d H:i:s'),
            'alterado_em' => $receita->alterado_em?->format('Y-m-d H:i:s'),
        ];
    }
}
