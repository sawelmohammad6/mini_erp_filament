<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $resources = [
            'Customer',
            'Product',
            'Order',
            'Expense',
            'ExpenseCategory',
        ];

        $resourceActions = ['viewAny', 'view', 'create', 'update', 'delete'];

        $pages = [
            'Settings',
            'SalesReport',
            'ExpenseReport',
            'ProfitReport',
            'InventoryReport',
        ];

        $widgets = [
            'DashboardAnalyticsWidget',
            'MonthlySalesChartWidget',
            'SalesVsExpenseChartWidget',
            'TopSellingProductsWidget',
            'RecentOrdersWidget',
            'RecentExpensesWidget',
            'RecentActivitiesWidget',
            'ProductStatsWidget',
            'OrderStatsWidget',
            'ExpenseStatsWidget',
            'CustomerStatsWidget',
            'UserStatsWidget',
            'QuickNotificationsWidget',
        ];

        foreach ($resources as $resource) {
            foreach ($resourceActions as $action) {
                Permission::firstOrCreate(['name' => "{$action}:{$resource}", 'guard_name' => 'web']);
            }
        }

        foreach ($pages as $page) {
            Permission::firstOrCreate(['name' => "view:{$page}", 'guard_name' => 'web']);
        }

        foreach ($widgets as $widget) {
            Permission::firstOrCreate(['name' => "view:{$widget}", 'guard_name' => 'web']);
        }

        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->givePermissionTo(Permission::all());

        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $managerPermissions = Permission::whereIn('name', [
            'viewAny:Customer', 'view:Customer', 'create:Customer', 'update:Customer',
            'viewAny:Product', 'view:Product', 'create:Product', 'update:Product',
            'viewAny:Order', 'view:Order', 'create:Order', 'update:Order',
            'viewAny:Expense', 'view:Expense', 'create:Expense', 'update:Expense',
            'viewAny:ExpenseCategory', 'view:ExpenseCategory', 'create:ExpenseCategory', 'update:ExpenseCategory',
            'view:SalesReport',
            'view:ExpenseReport',
            'view:ProfitReport',
            'view:InventoryReport',
        ])->get();
        $manager->syncPermissions($managerPermissions);

        $staff = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);
        $staffPermissions = Permission::whereIn('name', [
            'viewAny:Customer', 'view:Customer',
            'viewAny:Product', 'view:Product',
            'viewAny:Order', 'view:Order',
            'viewAny:Expense', 'view:Expense',
            'viewAny:ExpenseCategory', 'view:ExpenseCategory',
            'view:SalesReport',
            'view:ExpenseReport',
            'view:ProfitReport',
            'view:InventoryReport',
        ])->get();
        $staff->syncPermissions($staffPermissions);
    }
}
