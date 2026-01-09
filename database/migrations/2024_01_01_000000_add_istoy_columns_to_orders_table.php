<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Istoy\Services\OrderService;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration adds the required columns for Istoy package.
     * It safely handles both new tables and existing tables by checking
     * if columns already exist before adding them.
     */
    public function up(): void
    {
        // Get the table name from config or use default 'orders'
        $tableName = $this->getTableName();

        // Check if table exists, if not create it with all required columns
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('external_id')->nullable();
                $table->integer('service')->nullable();
                $table->string('link');
                $table->integer('quantity');
                $table->string('status')->default('pending');
                $table->integer('start_count')->default(0);
                $table->integer('remains')->default(0);
                $table->timestamps();
            });
            return;
        }

        // Table exists, add columns only if they don't exist
        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            if (!Schema::hasColumn($tableName, 'external_id')) {
                $table->string('external_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn($tableName, 'service')) {
                $table->integer('service')->nullable()->after('external_id');
            }
            if (!Schema::hasColumn($tableName, 'link')) {
                $table->string('link')->after('service');
            }
            if (!Schema::hasColumn($tableName, 'quantity')) {
                $table->integer('quantity')->after('link');
            }
            if (!Schema::hasColumn($tableName, 'status')) {
                $table->string('status')->default('pending')->after('quantity');
            }
            if (!Schema::hasColumn($tableName, 'start_count')) {
                $table->integer('start_count')->default(0)->after('status');
            }
            if (!Schema::hasColumn($tableName, 'remains')) {
                $table->integer('remains')->default(0)->after('start_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * Note: This only removes columns if the table was created by this migration.
     * For existing tables, columns are not removed to prevent data loss.
     */
    public function down(): void
    {
        $tableName = $this->getTableName();

        // Only drop table if it was created by this migration
        // For safety, we don't drop columns from existing tables
        if (Schema::hasTable($tableName)) {
            // Check if this is likely a table we created (has all our columns)
            $hasAllColumns = Schema::hasColumn($tableName, 'external_id') &&
                            Schema::hasColumn($tableName, 'service') &&
                            Schema::hasColumn($tableName, 'link') &&
                            Schema::hasColumn($tableName, 'quantity') &&
                            Schema::hasColumn($tableName, 'status') &&
                            Schema::hasColumn($tableName, 'start_count') &&
                            Schema::hasColumn($tableName, 'remains');

            // Only drop if it looks like our table (has all columns and no other custom columns)
            // For safety, we'll just comment this out - users should manually handle rollback
            // Schema::dropIfExists($tableName);
        }
    }

    /**
     * Get the table name from the Order model or config
     */
    protected function getTableName(): string
    {
        try {
            $orderFqn = OrderService::orderFqn();

            $model = app($orderFqn);
            return $model->getTable();
            
        } catch (Exception $e) {
            // Fallback to default if model can't be instantiated
        }

        // Default table name
        return 'orders';
    }
};

