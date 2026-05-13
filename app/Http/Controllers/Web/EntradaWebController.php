<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class EntradaWebController extends Controller
{
    private function apiUrl(string $path): string
    {
        return 'http://localhost/api/' . ltrim($path, '/');
    }

    public function index()
    {
        $response = Http::withToken(session('api_token'))
            ->accept('application/json')
            ->get($this->apiUrl('entradas'));

        $entradas = $response->successful() ? $response->json('data', []) : [];

        return view('entradas.index', compact('entradas'));
    }

    public function show($id)
    {
        $response = Http::withToken(session('api_token'))
            ->accept('application/json')
            ->get($this->apiUrl("entradas/{$id}"));

        if ($response->failed()) {
            abort(404, 'Entrada no encontrada');
        }

        $entrada = $response->json('data');

        return view('entradas.show', compact('entrada'));
    }
}
