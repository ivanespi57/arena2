<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class AdminWebController extends Controller
{
    private function apiUrl(string $path): string
    {
        return 'http://host.docker.internal/api/' . ltrim($path, '/');
    }

    private function token(): string
    {
        return session('api_token', '');
    }

    public function index()
    {
        $response = Http::withToken($this->token())
            ->accept('application/json')
            ->get($this->apiUrl('eventos'));

        $eventos = $response->successful() ? $response->json('data', []) : [];

        $sectoresRes = Http::withToken($this->token())
            ->accept('application/json')
            ->get($this->apiUrl('sectores'));

        $sectores = $sectoresRes->successful() ? $sectoresRes->json('data', []) : [];

        return view('admin.index', compact('eventos', 'sectores'));
    }

    public function editEvento($id)
    {
        $response = Http::withToken($this->token())
            ->accept('application/json')
            ->get($this->apiUrl("eventos/{$id}"));

        if ($response->failed()) {
            abort(404, 'Evento no encontrado');
        }

        $evento = $response->json('data.evento');

        return view('admin.eventos.edit', compact('evento'));
    }

    public function createSector()
    {
        return view('admin.sectores.create');
    }

    public function editSector($id)
    {
        return view('admin.sectores.edit', ['sectorId' => $id]);
    }
}
