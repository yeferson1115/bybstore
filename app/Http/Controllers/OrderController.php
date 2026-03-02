<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\OrderItemAddon;
use DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    // 1) Vista grilla mesas + boton crear llevar/domicilio
    public function index()
    {
        $tables = Table::orderBy('name')->get();
        return view('admin.orders.tables', compact('tables'));
    }

    // 2) Abrir mesa: crea pedido abierto si no existe
    public function openTable($id)
    {
        $table = Table::findOrFail($id);

        $order = Order::where('table_id',$id)->where('status','abierto')->first();

        if(!$order){
            $order = Order::create([
                'table_id' => $id,
                'type' => 'mesa',
                'total' => 0,
                'status' => 'abierto'
            ]);
            $table->update(['status'=>'disponible']);
        }

        $products = Product::orderBy('name')->paginate(12);

        return view('admin.orders.order', compact('table','order','products'));
    }

    // 3) Crear pedido para llevar (redirecciona a editor)
    public function createTakeaway()
    {
        $order = Order::create([
            'table_id' => null,
            'type' => 'llevar',
            'status' => 'abierto'
        ]);
        $products = Product::orderBy('name')->get();
        return redirect()->route('orders.edit', $order->id);
    }

    // 4) Form para crear pedido a domicilio (si quieres pedir datos antes)
    public function createDeliveryForm()
    {
        $order = Order::create([
            'table_id' => null,
            'type' => 'domicilio',
            'status' => 'abierto'
        ]);
        $products = Product::orderBy('name')->get();
        return redirect()->route('orders.edit', $order->id);
    }

    public function createDelivery(Request $request)
    {
        $data = $request->validate([
            'customer_name'=>'required|string|max:255',
            'customer_phone'=>'required|string|max:50',
            'customer_address'=>'required|string|max:500'
        ]);

        $order = Order::create([
            'type'=>'domicilio',
            'table_id'=>null,
            'status'=>'abierto',
            'customer_name'=>$data['customer_name'],
            'customer_phone'=>$data['customer_phone'],
            'customer_address'=>$data['customer_address']
        ]);

        return redirect()->route('orders.edit', $order->id);
    }

    // 5) Edit / show order (editor para mesa/llevar/domicilio)
    public function edit(Order $order)
    {
        $order->load('items.product','table');
        $products = Product::orderBy('name')->paginate(12);
        return view('admin.orders.order', compact('order','products'));
    }

    // 6) add item AJAX
   public function addItem(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string',
            'addons' => 'nullable|array',
            'addons.*' => 'integer|exists:product_addons,id',
        ]);
        $order=Order::find($data['order_id']);

        $product = Product::findOrFail($data['product_id']);

        // Crea el item
        $item = OrderItem::create([
            'order_id' => $data['order_id'],
            'product_id' => $product->id,
            'quantity' => $data['quantity'],
            'price' => $product->price,
            'note' => $data['note'] ?? null
        ]);

        // Guardamos addons por item
        if(isset($data['addons'])){
            foreach($data['addons'] as $addon){
                OrderItemAddon::create([
                    'order_item_id' => $item->id,
                    'product_addon_id' => $addon
                ]);
            }
        }

        // Recalcular total
        $this->updateOrderTotal($data['order_id']);

         if ($order->items()->count() === 1) {
            // Es el primer producto, marcar mesa como ocupada
            if ($order->table_id) {
                Table::where('id', $order->table_id)
                    ->update(['status' => 'ocupada']);
            }
        }

        return response()->json([
            'success'=>true,
            'item'=>$item->load('product','addons')
        ]);
    }


    // 7) update item AJAX (cantidad / nota)
// En tu método updateItem del controlador
public function updateItem(Request $request)
{
    $data = $request->validate([
        'id' => 'required|exists:order_items,id',
        'quantity' => 'required|integer|min:1|max:999', // Añadir máximo
        'note' => 'nullable|string',
        'addons' => 'nullable|array',
        'addons.*' => 'integer|exists:product_addons,id'
    ]);

    $item = OrderItem::findOrFail($data['id']);

    // actualizar item
    $item->update([
        'quantity' => $data['quantity'],
        'note' => $data['note'] ?? null
    ]);

    // borrar addons anteriores
    OrderItemAddon::where('order_item_id',$item->id)->delete();

    // registrar nuevos addons
    if(isset($data['addons'])){
        foreach($data['addons'] as $addonId){
            OrderItemAddon::create([
                'order_item_id' => $item->id,
                'product_addon_id' => $addonId
            ]);
        }
    }

    $this->updateOrderTotal($item->order_id);
    
    // Obtener el total actualizado
    $order = Order::find($item->order_id);

    return response()->json([
        'success' => true,
        'order_total' => $order->total
    ]);
}


    // 8) delete item AJAX
    public function deleteItem(Request $request)
    {
        $data = $request->validate(['id'=>'required|exists:order_items,id']);
        $item = OrderItem::findOrFail($data['id']);
        $orderId = $item->order_id;
        $item->delete();
        $this->updateOrderTotal($orderId);

        $order=Order::find($orderId);

         if ($order->items()->count() === 0) {
            // Es el primer producto, marcar mesa como ocupada
            if ($order->table_id) {
                Table::where('id', $order->table_id)
                    ->update(['status' => 'disponible']);
            }
        }
        return response()->json(['success'=>true]);
    }

    // 9) cambiar estado de mesa (disponible/ocupada/reservada)
    public function changeStatus(Request $request)
    {
        $data = $request->validate([
            'table_id'=>'required|exists:tables,id',
            'status'=>'required|in:disponible,ocupada,reservada'
        ]);
        $table = Table::findOrFail($data['table_id']);
        $table->update(['status'=>$data['status']]);
        return response()->json(['success'=>true]);
    }

    // 10) cerrar/ facturar pedido -> marcar status cerrado y liberar mesa si aplica
    public function closeOrder(Request $request, Order $order)
    {
        // Validación dinámica
        if ($order->type == 'domicilio') {
            $data = $request->validate([
                'note' => 'nullable|string',
                'customer_name' => 'required|string|max:255',
                'customer_phone' => 'required|string|max:50',
                'customer_address' => 'required|string|max:500',
                'payment_method' => 'required|in:Efectivo,Transferencia',
            ]);

            // Guardar datos de entrega
            $order->customer_name = $data['customer_name'];
            $order->customer_phone = $data['customer_phone'];
            $order->customer_address = $data['customer_address'];

        } else {
            $data = $request->validate([
                'note' => 'nullable|string',
                'payment_method' => 'required|in:Efectivo,Transferencia',
            ]);
        }

        // Actualizar nota
        $order->payment_method = $data['payment_method'] ?? $order->payment_method;
        $order->note = $data['note'] ?? $order->note;

        // Recalcular total
        $this->updateOrderTotal($order->id);

        // Cerrar pedido
        $order->status = 'cerrado';
        $order->paid = 1;
        $order->save();

        // Si es mesa → liberar mesa
        if ($order->table_id) {
            $order->table->update(['status' => 'disponible']);
        }

        return response()->json(['success' => true]);
    }


    // 11) generar ticket view simple
    public function ticket(Order $order)
    {
        $order->load('customer','items.product','items.addons.addon','table');

        return view('admin.orders.ticket', compact('order'));
    }


    // helper: recalcula total
    protected function updateOrderTotal($orderId)
    {
        $order = Order::find($orderId);
        if(!$order) return;

        $subtotalItems = $order->items()
            ->selectRaw('SUM(quantity * price) as t')
            ->value('t') ?? 0;

        // Sumamos addons por item
        $addonTotal = DB::table('order_item_addons as a')
    ->join('order_items as i', 'i.id', '=', 'a.order_item_id')
    ->join('product_addons as pa', 'pa.id', '=', 'a.product_addon_id')
    ->where('i.order_id', $orderId)
    ->selectRaw('SUM(pa.price * i.quantity) as t')
    ->value('t') ?? 0;


        $order->total = $subtotalItems + $addonTotal;
        $order->save();
    }

    public function updateCustomer(Request $request)
    {
        $order = Order::find($request->order_id);

        $order->customer_id = $request->customer_id ?: 0;
        $order->save();

        return response()->json(['success' => true]);
    }

    // Reporte: vista principal con filtros
public function reportIndex()
{
    // no cargamos todos los pedidos (AJAX lo hará)
    return view('admin.reports.orders_index');
}
public function reportData(Request $request)
{
    //$q = Order::query()->where('cancelled', false)->with('customer', 'table');
    $q = Order::query()->with('customer', 'table');

    // fecha rango
    if ($request->start_date && $request->end_date) {
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();
        $q->whereBetween('created_at', [$start, $end]);
    }

    // buscar por id, mesa, cliente, etc.
    if ($request->search) {
        $search = trim($request->search);
        
        // Intentar extraer número de mesa si busca "mesa X"
        $tableNumber = null;
        if (preg_match('/mesa\s*(\d+)/i', $search, $matches)) {
            $tableNumber = $matches[1];
        } elseif (is_numeric($search)) {
            $tableNumber = $search;
        }
        
        $q->where(function($query) use ($search, $tableNumber) {
            // 1. Buscar por ID de orden (exacto)
            if (is_numeric($search)) {
                $query->where('id', (int) $search);
            }
            
            // 2. Buscar por número de mesa
            if ($tableNumber) {
                $query->orWhereHas('table', function($t) use ($tableNumber) {
                    // Buscar mesa que contenga el número
                    $t->where('name', 'like', '%' . $tableNumber . '%');
                });
            }
            
            // 3. Buscar por nombre de cliente (parcial)
            $query->orWhere('customer_name', 'like', '%' . $search . '%');
            
            // 4. Buscar por teléfono (parcial)
            //$query->orWhere('customer_phone', 'like', '%' . $search . '%');
            
            // 5. Buscar por tipo de pedido (parcial)
            //$query->orWhere('type', 'like', '%' . $search . '%');
            
            // 6. Buscar en cliente relacionado
            /*$query->orWhereHas('customer', function($c) use ($search) {
                $c->where('name', 'like', '%' . $search . '%')
                  ->orWhere('document', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });*/
        });
    }

    $q->where('total', '>', 0);
    $orders = $q->orderBy('created_at', 'desc')->paginate(10);

    $paginationHtml = $orders->onEachSide(1)->links('vendor.pagination.bootstrap-5')->toHtml();

    return response()->json([
        'html' => view('admin.reports.partials.orders_rows', compact('orders'))->render(),
        'pagination' => $paginationHtml,
        'summary' => "Showing {$orders->firstItem()} to {$orders->lastItem()} of {$orders->total()} results"
    ]);
}

    /**
     * Reporte de órdenes anuladas
     */
    public function cancelledOrdersReport(Request $request)
    {
        // Filtrar directamente sin usar scope
        $q = Order::query()->where('cancelled', true)->with('customer', 'table');

        // fecha rango
        if ($request->start_date && $request->end_date) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            $q->whereBetween('cancelled_at', [$start, $end]);
        }

        // buscar por id o razón
        if ($request->search) {
            $s = trim($request->search);
            if (is_numeric($s)) {
                $q->where('id', $s);
            } else {
                $q->where('cancelled_reason', 'like', '%'.$s.'%');
            }
        }

        $orders = $q->orderBy('cancelled_at', 'desc')->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.reports.partials.cancelled_orders_rows', compact('orders'))->render(),
                'pagination' => $orders->onEachSide(1)->links('vendor.pagination.bootstrap-5')->toHtml()
            ]);
        }

        return view('admin.reports.cancelled_orders', compact('orders'));
    }

    /**
     * Método para cancelar orden
     */
    public function cancelOrder(Request $request, Order $order)
    {
        // Validar que la orden esté en estado válido para anular
        if ($order->status == 'cerrado') {
            return response()->json([
                'success' => false,
                'message' => 'No se puede anular una orden ya cerrada'
            ], 400);
        }

        $request->validate([
            'reason' => 'required|string|max:500',
            'confirm' => 'required'
        ]);

        if (!$request->confirm) {
            return response()->json([
                'success' => false,
                'message' => 'Debe confirmar la anulación'
            ]);
        }

        // Anular la orden
        $order->update([
            'cancelled' => true,
            'cancelled_at' => Carbon::now(),
            'cancelled_reason' => $request->reason,
            'status' => 'anulado'
        ]);

        // Liberar mesa si está asociada
        if ($order->table_id) {
            $order->table->update(['status' => 'disponible']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Orden anulada correctamente',
            'order' => $order
        ]);
    }

    /**
     * Método para restaurar orden
     */
    public function restoreOrder(Request $request, Order $order)
    {
        if (!$order->cancelled) {
            return response()->json([
                'success' => false,
                'message' => 'Esta orden no está anulada'
            ], 400);
        }

        $order->update([
            'cancelled' => false,
            'cancelled_at' => null,
            'cancelled_reason' => null,
            'status' => 'abierto'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Orden restaurada correctamente',
            'order' => $order
        ]);
    }

// Ver pedido (detalle / edición)
public function reportShow(Order $order)
{
    $order->load('customer','items.product','items.addons.addon','table');
    // vista que permite editar pedido (reusa tus partials)
    return view('admin.reports.order_show', compact('order'));
}

// Actualizar datos globales del pedido (nota, customer_name/phone/address, paid, shipping cost)
public function reportUpdate(Request $request, Order $order)
{
    $data = $request->validate([
        'note' => 'nullable|string',
        'customer_name' => 'nullable|string|max:255',
        'customer_phone' => 'nullable|string|max:50',
        'customer_address' => 'nullable|string|max:500',
        'paid' => 'nullable|boolean',
        'shipping_cost' => 'nullable|numeric|min:0',
        'payment_method'=>'nullable|string|max:50',
        'customer_id' => 'nullable',

    ]);

    // Si tipo domicilio, forzamos required server-side (solo si viene cerrar/pagar)
    if($order->type === 'domicilio' && $request->has('validate_domicilio')) {
        $request->validate([
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
            'customer_address' => 'required|string'
        ]);
    }

    // Guardar campos relevantes
    $order->note = $data['note'] ?? $order->note;
    $order->payment_method = $data['payment_method'] ?? $order->payment_method;
    $order->customer_name = $data['customer_name'] ?? $order->customer_name;
    $order->customer_phone = $data['customer_phone'] ?? $order->customer_phone;
    $order->customer_address = $data['customer_address'] ?? $order->customer_address;
    $order->customer_id = $data['customer_id'] ?? $order->customer_id;
    if (isset($data['paid'])){
        $order->paid = $data['paid'];
        if($data['paid']==1){
            $order->status= 'cerrado';
            if ($order->table_id) {
                Table::where('id', $order->table_id)
                    ->update(['status' => 'disponible']);
            }
        }else{
            $order->status= 'abierto';
        }
        
    }
    if (isset($data['shipping_cost'])) $order->shipping_cost = $data['shipping_cost'];

    $order->save();

    // Si se cambia shipping_cost, recalc total
    if (isset($data['shipping_cost'])) $this->recalculateOrderWithShipping($order);

    return response()->json(['success' => true, 'order' => $order]);
}

// actualizar costo de envío independiente
public function updateShipping(Request $request, Order $order)
{
    $data = $request->validate(['shipping_cost' => 'nullable|numeric|min:0']);
    $order->shipping_cost = $data['shipping_cost'] ?? 0;
    $order->save();
    $this->recalculateOrderWithShipping($order);
    return response()->json(['success'=>true,'total'=> number_format($order->total,2)]);
}

// marcar pagado
public function togglePaid(Request $request, Order $order)
{
    $order->paid = $request->paid ? 1 : 0;
    $order->save();
    return response()->json(['success'=>true,'paid'=>$order->paid]);
}

// helper: recalcula total incluyendo shipping (llama a tu updateOrderTotal y suma shipping)
protected function recalculateOrderWithShipping(Order $order)
{
    $this->updateOrderTotal($order->id); // ya recalcula base + addons
    $shipping = $order->shipping_cost ?? 0;
    //$order->total = ($order->total ?? 0) + $shipping;
    $order->total = ($order->total ?? 0);
    $order->save();
}

public function getOrderItem($id)
{
    $item = OrderItem::with('addons.addon','product.addons')->findOrFail($id);

    return response()->json([
        'id' => $item->id,
        'quantity' => $item->quantity,
        'note' => $item->note,
        'addons_selected' => $item->addons->pluck('product_addon_id'),
        'addons_available' => $item->product->addons
    ]);
}

// app/Http/Controllers/OrderController.php

public function updateNote(Request $request, Order $order)
{
    try {
        $request->validate([
            'note' => 'nullable|string|max:1000',
        ]);
        
        $order->update([
            'note' => $request->note,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Nota actualizada correctamente'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al guardar la nota'
        ], 500);
    }
}




}
