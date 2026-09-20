<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/get/user', fn (Request $r) => $r->user());
    Route::post('/logout', [AuthController::class, 'logout']);
});

/* -------------------------------------------------------------------------- */
/* MASTER PRODUCT BADGES ROUTES                                              */
/* -------------------------------------------------------------------------- */
Route::prefix('products/badges')->group(function () {
    Route::get('/', [ProductController::class, 'retrieveProductBadges']);
    Route::post('/', [ProductController::class, 'registerProductBadge']);
    Route::post('/{id}', [ProductController::class, 'modifyProductBadge'])->whereNumber('id');
    Route::delete('/{id}', [ProductController::class, 'removeProductBadge'])->whereNumber('id');
});

/* -------------------------------------------------------------------------- */
/* PRODUCTS ROUTES                                                            */
/* -------------------------------------------------------------------------- */
Route::get('get/products',                     [ProductController::class, 'getAllProducts']);   
Route::get('products/price-range',             [ProductController::class, 'getPriceRange']);
Route::get('products/category/{category}',     [ProductController::class, 'getProductsByCategory']);
Route::get('products/search/{term}',           [ProductController::class, 'searchProducts']);
Route::post('products',                        [ProductController::class, 'createProduct']);

// Dynamic {id} routes - whereNumber('id') சேர்க்கப்பட்டுள்ளது
Route::get('products/{id}',                    [ProductController::class, 'getProductById'])->whereNumber('id');
Route::match(['post', 'put'], 'products/{id}', [ProductController::class, 'updateProduct'])->whereNumber('id');
Route::delete('products/{id}',                 [ProductController::class, 'deleteProduct'])->whereNumber('id');

/* -------------------------------------------------------------------------- */
/* ORDERS ROUTES                                                              */
/* -------------------------------------------------------------------------- */
Route::prefix('orders')->group(function () {
    Route::get('/',                        [OrderController::class, 'getAllOrders']); 
    Route::get('/number/{orderNumber}',    [OrderController::class, 'getOrderByOrderNumber']); 
    Route::get('/{id}',                    [OrderController::class, 'getOrderById'])->whereNumber('id'); 
    Route::post('/',                       [OrderController::class, 'createOrder']);
    Route::delete('/delete/{id}', [OrderController::class, 'deleteOrder']);
    Route::put('/{id}/status',             [OrderController::class, 'updateOrderStatus'])->whereNumber('id'); 
    Route::get('/{id}/whatsapp-link',      [OrderController::class, 'getWhatsAppLink'])->whereNumber('id'); 
    Route::put('/{id}/whatsapp-sent', [OrderController::class, 'markWhatsAppSent'])->whereNumber('id');
});

/* -------------------------------------------------------------------------- */
/* POS BILLS ROUTES                                                           */
/* -------------------------------------------------------------------------- */
Route::prefix('pos/bills')->group(function () { 
    Route::get('/',        [ProductController::class, 'getAllBills']);
    Route::get('/{id}',    [ProductController::class, 'getBillById'])->whereNumber('id');
    Route::post('/',       [ProductController::class, 'createBill']);
    Route::put('/{id}',    [ProductController::class, 'updateBill'])->whereNumber('id');
    Route::delete('/{id}', [ProductController::class, 'deleteBill'])->whereNumber('id');
});

/* -------------------------------------------------------------------------- */
/* COMBO OFFERS ROUTES                                                        */
/* -------------------------------------------------------------------------- */
Route::prefix('combo-offers')->group(function () {
    Route::get('/',        [ProductController::class, 'getAllCombos']);
    Route::get('/{id}',    [ProductController::class, 'getComboById'])->whereNumber('id');
    Route::post('/',       [ProductController::class, 'createCombo']);
    Route::match(['post', 'put'], '/{id}', [ProductController::class, 'updateCombo'])->whereNumber('id');
    Route::delete('/{id}', [ProductController::class, 'deleteCombo'])->whereNumber('id');
});

// routes/api.php

Route::get('/categories',      [ProductController::class, 'listAllCategories']);
Route::get('/categories/{id}', [ProductController::class, 'getCategoryById']);

Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    Route::get('/categories',          [ProductController::class, 'listAllCategories']);
    Route::post('/categories',         [ProductController::class, 'createCategory']);
    Route::post('/categories/{id}',    [ProductController::class, 'updateCategory']); 
    Route::delete('/categories/{id}',  [ProductController::class, 'deleteCategory']);
});

// Route::middleware(['delay'])->group(function () {
//     Route::get('products', [ProductController::class, 'getAllProducts']);

// });

// Route::get('products', [ProductController::class, 'getAllProducts']);
// Route::get('product/{id}', [ProductController::class, 'getProductById']);
// Route::post('product', [ProductController::class, 'createProduct']);
// Route::put('product/{id}', [ProductController::class, 'updateProduct']);
// Route::delete('product/{id}', [ProductController::class, 'deleteProduct']);
// Route::get('products/category/{category}', [ProductController::class, 'getProductsByCategory']);
// Route::get('products/search/{term}', [ProductController::class, 'searchProducts']);

// Route::post('/orders', [OrderController::class, 'store']); //
// Route::get('/get/orders', [OrderController::class, 'getAllOrders']); 
// Route::get('/get/orders/{id}', [OrderController::class, 'getOrderDetail']); //
// Route::put('/get/orders/{id}', [OrderController::class, 'updateOrderStatus']);
// Route::get('/get/orders/number/{orderNumber}',[OrderController::class, 'getOrderByOrderNumber']);
// Route::get('/orders/{id}/whatsapp', [OrderController::class, 'getWhatsAppLink']);
// Route::post('/orders/{id}/whatsapp-sent', [OrderController::class, 'markWhatsAppSent']);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/set-password', [AuthController::class, 'setPassword']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('hero-sliders',         [DashboardController::class, 'listAllSliders']);
Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {
    Route::post('hero-sliders',        [DashboardController::class, 'createSlider']);
    Route::post('hero-sliders/{id}',   [DashboardController::class, 'updateSlider']);
    Route::delete('hero-sliders/{id}', [DashboardController::class, 'deleteSlider']);
});

Route::get('/pincode/{pincode}', [CommonController::class, 'lookup'])
    ->where('pincode', '[0-9]{6}');

// Route::prefix('auth')->group(function () {
//     Route::post('/login',        [AuthController::class, 'login']);
//     Route::post('/send-otp',     [AuthController::class, 'sendOtp']);
//     Route::post('/verify-otp',   [AuthController::class, 'verifyOtp']);
//     Route::post('/set-password', [AuthController::class, 'setPassword']);
//     Route::post('/register',     [AuthController::class, 'register']);
//     Route::post('/logout',       [AuthController::class, 'logout'])->middleware('auth:sanctum');
// });
 
// Route::get('/health', function () {
//     return response()->json([
//         'status' => 'ok',
//         'app' => config('app.name'),
//         'env' => app()->environment(),
//         'time' => now()->toDateTimeString(),
//     ]);
// });

// Route::get('/env-check', function () {
//     return [
//         'db_host' => env('DB_HOST'),
//         'config_db_host' => config('database.connections.pgsql.host'),
//     ];
// });

// Route::get('/smtp-check', function () {
//     return response()->json([
//         'mailer' => config('mail.default'),
//         'host' => config('mail.mailers.smtp.host'),
//         'port' => config('mail.mailers.smtp.port'),
//         'encryption' => config('mail.mailers.smtp.encryption'),
//         'from' => config('mail.from.address'),
//         'admin_mail' => config('mail.from.address') ? 'SET' : 'NOT SET',
//     ]);
// });

// Route::get('/mail-test', function () {
//     try {
//         Mail::raw('Live mail test OK', function ($m) {
//             $m->to('kmvfireworks@gmail.com')
//               ->subject('SMTP Test');
//         });

//         return response()->json([
//             'status' => 'sent'
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'status' => 'failed',
//             'error' => $e->getMessage()
//         ], 500);
//     }
// });

// Route::get('/db-check', function () {
//     try {
//         DB::connection()->getPdo();
//         return ['db' => 'connected'];
//     } catch (\Exception $e) {
//         return [
//             'db' => 'failed',
//             'error' => $e->getMessage()
//         ];
//     }
// });

// Route::get('/env-check', function () {
//     return [
//         'url' => env('DATABASE_URL'),
//         'config' => config('database.connections.pgsql.url'),
//     ];
// });


// Route::get('/db-test', function () {
//     try {
//         DB::connection()->select('select 1');

//         return response()->json([
//             'db' => 'connected',
//             'connection' => config('database.default'),
//             'host' => config('database.connections.pgsql.host'),
//         ]);
//     } catch (\Throwable $e) {
//         return response()->json([
//             'db' => 'failed',
//             'error' => $e->getMessage(),
//             'connection' => config('database.default'),
//             'host' => config('database.connections.pgsql.host'),
//         ], 500);
//     }
// });

// Route::get('/env-test', function () {
//     return response()->json([
//         'app_env'        => app()->environment(),
//         'app_url'        => config('app.url'),
//         'db_connection'  => config('database.default'),
//         'db_host'        => config('database.connections.pgsql.host'),
//         'db_port'        => config('database.connections.pgsql.port'),
//         'db_database'    => config('database.connections.pgsql.database'),
//         'session_driver' => config('session.driver'),
//         'cache_driver'   => config('cache.default'),
//     ]);
// });
// Route::get('/debug/env', function () {
//     return [
//         'app_env' => config('app.env'),
//         'app_url' => config('app.url'),

//         'db_connection' => config('database.default'),
//         'db_host' => config('database.connections.pgsql.host'),
//         'db_port' => config('database.connections.pgsql.port'),
//         'db_database' => config('database.connections.pgsql.database'),

//         'cache_driver' => config('cache.default'),
//         'session_driver' => config('session.driver'),
//     ];
// });

