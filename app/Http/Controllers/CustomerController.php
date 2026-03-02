<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

    // Mostrar lista de clientes
    public function index()
    {
        $customers = Customer::orderBy('name')->paginate(10);
        return view('admin.customers.index', compact('customers'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        return view('admin.customers.create');
    }

    // Guardar nuevo cliente
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'document' => 'nullable|string|max:50|unique:customers,document',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|string|email|max:120|unique:customers,email',
        ]);

        Customer::create($request->all());

        return redirect()->route('customers.index')
            ->with('success', 'Cliente creado exitosamente.');
    }

    // Mostrar detalles de un cliente
    public function show(Customer $customer)
    {
        return view('admin.customers.show', compact('customer'));
    }

    // Mostrar formulario de edición
    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    // Actualizar cliente
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'document' => 'nullable|string|max:50|unique:customers,document,' . $customer->id,
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|string|email|max:120|unique:customers,email,' . $customer->id,
        ]);

        $customer->update($request->all());

        return redirect()->route('customers.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    // Eliminar cliente (soft delete)
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Cliente eliminado exitosamente.');
    }

    // Método para restaurar cliente eliminado
    public function restore($id)
    {
        $customer = Customer::withTrashed()->find($id);
        $customer->restore();

        return redirect()->route('customers.index')
            ->with('success', 'Cliente restaurado exitosamente.');
    }

    // Guardado rápido desde el POS
    public function quickSave(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'document' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|string|max:120',
        ]);

        $customer = Customer::create($request->all());

        return response()->json([
            'success' => true,
            'customer' => $customer
        ]);
    }
}
