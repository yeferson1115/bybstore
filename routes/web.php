<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\CreditApplicationController;
use App\Http\Controllers\CompanyController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/solicitud-credito', [CreditApplicationController::class, 'create'])->name('credit-applications.create');
Route::post('/solicitud-credito', [CreditApplicationController::class, 'store'])->name('credit-applications.store');
Route::post('/solicitud-credito/retomar', [CreditApplicationController::class, 'resume'])->name('credit-applications.resume');
Route::get('/solicitud-credito/{creditApplication}/pdf', [CreditApplicationController::class, 'downloadPdf'])->name('credit-applications.pdf');

Route::middleware('auth')->group(function () {  
  Route::get('/dashboard', [DashboardController::class, 'index'])->name('index');

   Route::post('orders/{order}/cancel', [OrderController::class, 'cancelOrder'])->name('orders.cancel');
    Route::post('orders/{order}/restore', [OrderController::class, 'restoreOrder'])->name('orders.restore');
    Route::get('reports/orders/cancelled', [OrderController::class, 'cancelledOrdersReport'])->name('reports.orders.cancelled');

   
  // Resources
    
    Route::resource('users', UserController::class);
    Route::resource('permission', PermissionController::class);
    Route::get('/roles/{roleId}/permissions/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::put('/roles/{roleId}/permissions', [PermissionController::class, 'update'])->name('permissions.update');
    Route::resource('tables', TableController::class);
    Route::resource('companies', CompanyController::class)->except(['show']);
    Route::get('/products/{id}/addons', [ProductController::class, 'getAddons']);
    Route::get('/products/filter/list', [ProductController::class, 'filterProducts'])
    ->name('products.filter');
    Route::post('/customers/quick-save', [CustomerController::class, 'quickSave'])->name('customers.quickSave');
    Route::post('/orders/update-customer', [OrderController::class, 'updateCustomer'])->name('orders.updateCustomer');



    // POS
    Route::get('pos', [POSController::class,'index'])->name('pos.index');
    Route::post('pos/add-item', [POSController::class,'addItem'])->name('pos.add_item');
    Route::post('pos/remove-item', [POSController::class,'removeItem'])->name('pos.remove_item');
    Route::post('pos/create-order', [POSController::class,'createOrder'])->name('pos.create_order');

    // Orders
    Route::get('/orders', [OrderController::class,'index'])->name('orders.index');                    // grilla mesas
    Route::get('/orders/table/{id}', [OrderController::class,'openTable'])->name('orders.openTable'); // abrir mesa / pedido en mesa
    Route::get('/orders/create/takeaway', [OrderController::class,'createTakeaway'])->name('orders.takeaway');
    Route::get('/orders/create/delivery', [OrderController::class,'createDeliveryForm'])->name('orders.delivery.form');
    Route::post('/orders/create/delivery', [OrderController::class,'createDelivery'])->name('orders.delivery.create');

    Route::get('/orders/{order}', [OrderController::class,'edit'])->name('orders.edit'); // editar pedido (mesas/llevar/domicilio)
    Route::post('/orders/add-item', [OrderController::class,'addItem'])->name('orders.addItem');
    Route::post('/orders/update-item', [OrderController::class,'updateItem'])->name('orders.updateItem');
    Route::post('/orders/delete-item', [OrderController::class,'deleteItem'])->name('orders.deleteItem');
    Route::post('/orders/change-status', [OrderController::class,'changeStatus'])->name('orders.changeStatus');
    Route::post('/orders/close/{order}', [OrderController::class,'closeOrder'])->name('orders.close');
    Route::get('/orders/ticket/{order}', [OrderController::class,'ticket'])->name('orders.ticket');

   
    // Reporte
    Route::get('/reports/orders', [OrderController::class, 'reportIndex'])->name('reports.orders.index');
    Route::get('/reports/orders/data', [OrderController::class, 'reportData'])->name('reports.orders.data');

    // Ver / editar pedido desde reporte
    Route::get('/reports/orders/{order}', [OrderController::class, 'reportShow'])->name('reports.orders.show');
    Route::post('/reports/orders/{order}/update', [OrderController::class, 'reportUpdate'])->name('reports.orders.update');

    // Acciones AJAX
    Route::post('/reports/orders/{order}/shipping', [OrderController::class, 'updateShipping'])->name('reports.orders.shipping');
    Route::post('/reports/orders/{order}/toggle-paid', [OrderController::class, 'togglePaid'])->name('reports.orders.togglePaid');
    //Route::get('/order-items/{id}', [OrderItemController::class, 'show']);
    Route::get('/order-items/{id}', [OrderController::class,'getOrderItem']);
    
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/sales/export', [ReportController::class, 'exportSales'])->name('reports.sales.export');
    Route::get('/reportes/ventas/export', [ReportController::class, 'exportSales'])
    ->name('reports.sales.export');

    // Rutas para clientes
    Route::resource('customers', CustomerController::class);

    // Ruta adicional para restaurar clientes eliminados
    Route::get('customers/{id}/restore', [CustomerController::class, 'restore'])
        ->name('customers.restore');
    Route::post('/orders/{order}/update-note', [OrderController::class, 'updateNote'])->name('orders.updateNote');
   


});

Route::middleware(['auth'])->get('/api/products/{product}/addons', function(\App\Models\Product $product){
    return $product->addons()->get(['id','name','price']);
});

// En routes/web.php
Route::middleware('guest')->group(function () {
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});


Route::get('/clear-cache', function () {
  echo Artisan::call('config:clear');
  echo Artisan::call('config:cache');
  echo Artisan::call('cache:clear');
  echo Artisan::call('route:clear');
  echo Artisan::call('view:clear');
});