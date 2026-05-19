<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use Illuminate\Http\Request;

class SectorController extends Controller
{
    // Lista los sectores activos con el total de asientos de cada uno
    public function index()
    {
        $sectores = Sector::activos()
            ->withCount('asientos')
            ->get();

        return response()->json([
            'data' => $sectores,
        ]);
    }

    // Crea un sector; el nombre debe ser único en la tabla
    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255|unique:sectores',
            'descripcion' => 'nullable|string',
            'activo'      => 'boolean',
        ]);

        $sector = Sector::create($request->all());

        return response()->json([
            'data'    => $sector,
            'message' => 'Sector creado correctamente',
        ], 201);
    }

    // Actualiza el sector ignorando la unicidad de nombre sobre sí mismo
    public function update(Request $request, $id)
    {
        $sector = Sector::findOrFail($id);

        $request->validate([
            'nombre'      => 'sometimes|string|max:255|unique:sectores,nombre,' . $id,
            'descripcion' => 'nullable|string',
            'activo'      => 'boolean',
        ]);

        $sector->update($request->all());

        return response()->json([
            'data'    => $sector,
            'message' => 'Sector actualizado correctamente',
        ]);
    }

    // Elimina el sector solo si no tiene asientos asociados
    public function destroy($id)
    {
        $sector = Sector::findOrFail($id);

        if ($sector->totalAsientos() > 0) {
            return response()->json([
                'error' => 'No se puede eliminar un sector con asientos',
            ], 400);
        }

        $sector->delete();

        return response()->json([
            'message' => 'Sector eliminado correctamente',
        ]);
    }
}
