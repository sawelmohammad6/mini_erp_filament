<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Activity logs
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index('user_id', 'idx_activity_logs_user_id');
            $table->index('action', 'idx_activity_logs_action');
            $table->index('created_at', 'idx_activity_logs_created_at');
        });

        // Customers
        Schema::table('customers', function (Blueprint $table) {
            $table->index('name', 'idx_customers_name');
            $table->index('email', 'idx_customers_email');
            $table->index('is_active', 'idx_customers_is_active');
            $table->index('created_at', 'idx_customers_created_at');
        });

        // Products
        Schema::table('products', function (Blueprint $table) {
            $table->index('name', 'idx_products_name');
            $table->index('status', 'idx_products_status');
            $table->index('stock_quantity', 'idx_products_stock_quantity');
            $table->index('created_at', 'idx_products_created_at');
        });

        // Orders
        Schema::table('orders', function (Blueprint $table) {
            $table->index('customer_id', 'idx_orders_customer_id');
            $table->index('status', 'idx_orders_status');
            $table->index(['status', 'created_at'], 'idx_orders_status_created_at');
            $table->index('created_at', 'idx_orders_created_at');
        });

        // Order items
        Schema::table('order_items', function (Blueprint $table) {
            $table->index('order_id', 'idx_order_items_order_id');
            $table->index('product_id', 'idx_order_items_product_id');
            $table->index(['order_id', 'product_id'], 'idx_order_items_order_product');
        });

        // Expenses
        Schema::table('expenses', function (Blueprint $table) {
            $table->index('expense_category_id', 'idx_expenses_category_id');
            $table->index('expense_date', 'idx_expenses_expense_date');
            $table->index('created_at', 'idx_expenses_created_at');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('idx_activity_logs_user_id');
            $table->dropIndex('idx_activity_logs_action');
            $table->dropIndex('idx_activity_logs_created_at');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('idx_customers_name');
            $table->dropIndex('idx_customers_email');
            $table->dropIndex('idx_customers_is_active');
            $table->dropIndex('idx_customers_created_at');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_name');
            $table->dropIndex('idx_products_status');
            $table->dropIndex('idx_products_stock_quantity');
            $table->dropIndex('idx_products_created_at');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_customer_id');
            $table->dropIndex('idx_orders_status');
            $table->dropIndex('idx_orders_status_created_at');
            $table->dropIndex('idx_orders_created_at');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('idx_order_items_order_id');
            $table->dropIndex('idx_order_items_product_id');
            $table->dropIndex('idx_order_items_order_product');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex('idx_expenses_category_id');
            $table->dropIndex('idx_expenses_expense_date');
            $table->dropIndex('idx_expenses_created_at');
        });
    }
};
