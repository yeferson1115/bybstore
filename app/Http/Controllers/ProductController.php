<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductAddon;
use Illuminate\Support\Facades\Storage;
use DB;

class ProductController extends Controller {

    public function index() {
        $products = Product::with('category','addons')->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function create() {
        $categories = \App\Models\Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'sku' => 'nullable|string|max:100',
            'price' => 'required|numeric',
            'note' => 'nullable|string',
            'single_option' => 'nullable|boolean',
            'image' => 'nullable|image|max:2048',
            'addons' => 'array'
        ]);

        if($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('productos'), $filename); // Guarda en public/products
            $data['image'] = 'productos/' . $filename; // Ruta relativa para usar en la web
        }

        DB::beginTransaction();
        try {
            $product = Product::create($data);

            // addons: expect [{name,price},...]
            if($request->filled('addons')) {
                foreach($request->input('addons') as $a) {
                    if(!empty($a['name'])) {
                        ProductAddon::create([
                            'product_id' => $product->id,
                            'name' => $a['name'],
                            'price' => $a['price'] ?? 0
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('products.index')->with('success','Producto creado');
        } catch(\Exception $e) {
            DB::rollBack();
            return back()->withErrors($e->getMessage());
        }
    }

    public function edit(Product $product) {
        $categories = \App\Models\Category::orderBy('name')->get();
        $product->load('addons');
        return view('admin.products.edit', compact('product','categories'));
    }

    public function update(Request $request, Product $product) {
       
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'sku' => 'nullable|string|max:100',
            'price' => 'required|numeric',
            'note' => 'nullable|string',
            'single_option' => 'nullable|boolean',
            'image' => 'nullable|image|max:2048',
            'addons' => 'array'
        ]);
        

        if($request->hasFile('image')) {
            // borrar anterior si existe
            if($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }

            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('productos'), $filename); // guarda en public/products
            $data['image'] = 'productos/' . $filename; // ruta relativa para usar en la web
        }
        DB::beginTransaction();
        try {
            $product->update($data);
            
            // Si mandan addons, eliminamos los viejos y creamos nuevos (simple)
            if($request->filled('addons')) {   
                          
                $product->addons()->delete();
                
                foreach($request->input('addons') as $a) {                     
                    if(!empty($a['name'])) {                       
                        ProductAddon::create([
                            'product_id' => $product->id,
                            'name' => $a['name'],
                            'price' => $a['price'] ?? 0
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('products.index')->with('success','Producto actualizado');
        } catch(\Exception $e) {
            DB::rollBack();
            return back()->withErrors($e->getMessage());
        }
    }

    public function destroy(Product $product) {
        if($product->image) Storage::disk('public')->delete($product->image);
        $product->delete();
        return redirect()->route('products.index')->with('success','Producto eliminado');
    }

    public function getAddons($id)
    {
        $product = Product::with('addons')->findOrFail($id);

        return response()->json($product->addons);
    }

    public function filterProducts(Request $request)
    {
        $query = Product::query();

        // Filtro por categoría
        if ($request->category_id && $request->category_id != "all") {
            $query->where('category_id', $request->category_id);
        }

        // Buscador por nombre
        if ($request->search) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        // obtener productos con paginación AJAX
        $products = $query->orderBy('name')->paginate(12);

        // devolver HTML listo para insertar
        return response()->json([
            'html' => view('admin.orders.partials.products', compact('products'))->render(),
            'pagination' => (string) $products->links()
        ]);
    }

}
