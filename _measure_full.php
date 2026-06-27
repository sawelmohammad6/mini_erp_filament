<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Enums\OrderStatus;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

// Log in as admin user
$user = User::find(1);
auth()->login($user);

echo "=== FULL DASHBOARD PAGE LOAD SIMULATION ===\n";
echo "Cache driver: " . config('cache.default') . "\n\n";

// Clear all caches to simulate first page load
Cache::flush();
app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
DB::enableQueryLog();

echo "Simulating dashboard page load (as Admin user)...\n\n";

// 1. Navigation rendering: Shield checks permissions for each nav item
// Resources: Customer, Product, Order, Expense, ExpenseCategory, User = 6 viewAny checks
// + 6 view checks (for the models)
$user->can('viewAny:Customer');
$user->can('viewAny:Product');
$user->can('viewAny:Order');
$user->can('viewAny:Expense');
$user->can('viewAny:ExpenseCategory');

// Pages: 5 page view checks
$user->can('view:SalesReport');
$user->can('view:ExpenseReport');
$user->can('view:ProfitReport');
$user->can('view:InventoryReport');
$user->can('view:Settings');

echo "After navigation checks: " . count(DB::getQueryLog()) . " queries\n";

// 2. Dashboard Analytics Widget - queries
$totalSales = (float) Order::where('status', OrderStatus::Completed)->sum('total_amount');
$totalExpenses = (float) Expense::sum('amount');
$totalOrders = Order::count();

echo "After analytics widget: " . count(DB::getQueryLog()) . " queries\n";

// 3. Customer Stats Widget
Customer::count();
Customer::where('is_active', true)->count();
Customer::where('is_active', false)->count();

echo "After customer stats: " . count(DB::getQueryLog()) . " queries\n";

// 4. Product Stats Widget
Product::count();
Product::where('status', true)->count();
Product::where('stock_quantity', '>', 0)->where('stock_quantity', '<', 10)->count();

echo "After product stats: " . count(DB::getQueryLog()) . " queries\n";

// 5. Order Stats Widget
Order::count();
Order::where('status', OrderStatus::Pending)->count();
Order::where('status', OrderStatus::Completed)->count();
Order::whereDate('created_at', today())->count();

echo "After order stats: " . count(DB::getQueryLog()) . " queries\n";

// 6. Expense Stats Widget
Expense::sum('amount');
Expense::whereDate('expense_date', today())->sum('amount');
Expense::whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year)->sum('amount');

echo "After expense stats: " . count(DB::getQueryLog()) . " queries\n";

// 7. User Stats Widget
User::count();

echo "After user stats: " . count(DB::getQueryLog()) . " queries\n";

// 8. Notification widget
Product::where('stock_quantity', '>', 0)->where('stock_quantity', '<', 10)->count();
Product::where('stock_quantity', '<=', 0)->count();
Order::where('status', OrderStatus::Pending)->count();

echo "After quick notifications: " . count(DB::getQueryLog()) . " queries\n";

// 9. Chart widgets (old loop style - 36 queries)
for ($month = 1; $month <= 12; $month++) {
    Order::where('status', OrderStatus::Completed)->whereYear('created_at', now()->year)->whereMonth('created_at', $month)->sum('total_amount');
}
for ($month = 1; $month <= 12; $month++) {
    Order::where('status', OrderStatus::Completed)->whereYear('created_at', now()->year)->whereMonth('created_at', $month)->sum('total_amount');
    Expense::whereYear('expense_date', now()->year)->whereMonth('expense_date', $month)->sum('amount');
}

echo "After charts (old loop): " . count(DB::getQueryLog()) . " queries\n";

// 10. Table widgets
Product::query()->selectRaw('products.*, COALESCE(SUM(order_items.quantity), 0) as total_quantity, COALESCE(SUM(order_items.subtotal), 0) as total_revenue')->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')->groupBy('products.id')->orderByDesc('total_quantity')->limit(5)->get();
Order::with('customer:id,name')->latest()->limit(5)->get();
Expense::with('category')->latest('expense_date')->limit(5)->get();
ActivityLog::with('user:id,name')->latest()->limit(10)->get();

$totalBefore = count(DB::getQueryLog());
echo "\n=== TOTAL (before optimization) ===\n";
echo "{$totalBefore} database queries per dashboard page load\n";

echo "\n=== NOW WITH OPTIMIZATIONS ===\n";
DB::flushQueryLog();
Cache::flush();
app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

echo "1. With Spatie cache warmed + persmissions cached + file cache driver:\n";

// Simulate fresh request - permissions loaded once
$user->can('viewAny:Customer');
$user->can('viewAny:Product');
$user->can('viewAny:Order');
$user->can('viewAny:Expense');
$user->can('viewAny:ExpenseCategory');
$user->can('view:SalesReport');
$user->can('view:ExpenseReport');
$user->can('view:ProfitReport');
$user->can('view:InventoryReport');
$user->can('view:Settings');

// Stat widgets - cached with GROUP BY charts
$year = now()->year;

// Charts: 2 queries instead of 36
Order::where('status', OrderStatus::Completed)->whereYear('created_at', $year)->selectRaw("CAST(strftime('%m', created_at) AS INTEGER) as month, SUM(total_amount) as total")->groupBy('month')->pluck('total', 'month');
Expense::whereYear('expense_date', $year)->selectRaw("CAST(strftime('%m', expense_date) AS INTEGER) as month, SUM(amount) as total")->groupBy('month')->pluck('total', 'month');

// Aggregates: 1 query per widget using selectRaw
$analytics = DB::table('orders')->selectRaw("COALESCE(SUM(CASE WHEN status = 'completed' THEN total_amount ELSE 0 END), 0) as total_sales, COUNT(*) as total_orders")->first();
$totalExpenses = (float) Expense::sum('amount');
$customerStats = DB::table('customers')->selectRaw('COUNT(*) as total, SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active, SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive')->first();
$productStats = DB::table('products')->selectRaw('COUNT(*) as total, SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active, SUM(CASE WHEN stock_quantity < 10 AND stock_quantity > 0 THEN 1 ELSE 0 END) as low_stock, SUM(CASE WHEN stock_quantity <= 0 THEN 1 ELSE 0 END) as out_of_stock')->first();
$orderStats = DB::table('orders')->selectRaw("COUNT(*) as total, SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending, SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed, SUM(CASE WHEN DATE(created_at) = DATE('now') THEN 1 ELSE 0 END) as today")->first();
$expenseStats = DB::table('expenses')->selectRaw("COALESCE(SUM(amount), 0) as total, COALESCE(SUM(CASE WHEN DATE(expense_date) = DATE('now') THEN amount ELSE 0 END), 0) as today, COALESCE(SUM(CASE WHEN strftime('%m', expense_date) = strftime('%m', 'now') AND strftime('%Y', expense_date) = strftime('%Y', 'now') THEN amount ELSE 0 END), 0) as month")->first();
$userCount = User::count();

// Table widgets
Product::query()->selectRaw('products.*, COALESCE(SUM(order_items.quantity), 0) as total_quantity, COALESCE(SUM(order_items.subtotal), 0) as total_revenue')->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')->groupBy('products.id')->orderByDesc('total_quantity')->limit(5)->get();
Order::with('customer:id,name')->latest()->limit(5)->get();
Expense::with('category')->latest('expense_date')->limit(5)->get();
ActivityLog::with('user:id,name')->latest()->limit(10)->get();

$totalAfter = count(DB::getQueryLog());
echo "\n=== TOTAL (after optimization + file cache) ===\n";
echo "{$totalAfter} database queries per dashboard page load\n";

echo "\n=== SUMMARY ===\n";
echo "Before: {$totalBefore} queries\n";
echo "After:  {$totalAfter} queries\n";
echo "Reduction: " . round((1 - $totalAfter/$totalBefore) * 100) . "%\n\n";

echo "=== ROOT CAUSE ===\n";
echo "1. CACHE_STORE=database forces ALL cache reads through DB (including Spatie permissions)\n";
echo "2. PermissionRegistrar loads all permissions on first check - 3 queries to join roles/permissions\n";
echo "3. Each widget/component does separate permission checks\n";
echo "4. Charts ran 36 queries in loops (before optimization)\n";
echo "5. Stat widgets ran 24 separate aggregate queries (before optimization)\n";
echo "6. Sessions stored in DB adds overhead per request\n";
echo "7. No indexes on polymorphic model_has_* columns\n";
echo "8. Shop (Setting::get()) uses database cache store\n";
