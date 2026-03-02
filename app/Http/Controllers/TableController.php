<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::orderBy('id', 'DESC')->get();
        return view('admin.tables.index', compact('tables'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'   => 'required|string|max:255',            
            'status' => 'required|in:disponible,ocupada,reservada',
            'note'   => 'nullable|string'
        ]);

        $table = Table::create($data);

        return response()->json(['success' => true, 'message' => 'Mesa creada correctamente']);
    }

    public function show(Table $table)
    {
        return response()->json(['table' => $table]);
    }

    public function update(Request $request, Table $table)
    {
        $data = $request->validate([
            'name'   => 'required|string|max:255',
            'status' => 'required|in:disponible,ocupada,reservada',
            'note'   => 'nullable|string'
        ]);

        $table->update($data);

        return response()->json(['success' => true, 'message' => 'Mesa actualizada correctamente']);
    }

    public function destroy(Table $table)
    {
        $table->delete();

        return response()->json(['success' => true, 'message' => 'Mesa eliminada correctamente']);
    }
}
