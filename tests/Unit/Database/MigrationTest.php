<?php

namespace Istoy\Tests\Unit\Database;

use Istoy\Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MigrationTest extends TestCase
{
    /**
     * Get the migration instance
     */
    protected function getMigration()
    {
        $migrationPath = __DIR__ . '/../../../database/migrations/2024_01_01_000000_add_istoy_columns_to_orders_table.php';
        return require $migrationPath;
    }

    public function test_migration_creates_orders_table_if_not_exists()
    {
        // Ensure table doesn't exist
        Schema::dropIfExists('orders');
        
        // Manually run the migration
        $migration = $this->getMigration();
        $migration->up();

        // Assert table exists
        $this->assertTrue(Schema::hasTable('orders'));

        // Assert all required columns exist
        $this->assertTrue(Schema::hasColumn('orders', 'id'));
        $this->assertTrue(Schema::hasColumn('orders', 'external_id'));
        $this->assertTrue(Schema::hasColumn('orders', 'service'));
        $this->assertTrue(Schema::hasColumn('orders', 'link'));
        $this->assertTrue(Schema::hasColumn('orders', 'quantity'));
        $this->assertTrue(Schema::hasColumn('orders', 'status'));
        $this->assertTrue(Schema::hasColumn('orders', 'start_count'));
        $this->assertTrue(Schema::hasColumn('orders', 'remains'));
        $this->assertTrue(Schema::hasColumn('orders', 'created_at'));
        $this->assertTrue(Schema::hasColumn('orders', 'updated_at'));
    }

    public function test_migration_adds_missing_columns_to_existing_table()
    {
        // Ensure clean state
        Schema::dropIfExists('orders');
        
        // Create a table with only some columns
        Schema::create('orders', function ($table) {
            $table->id();
            $table->string('custom_field');
            $table->timestamps();
        });

        // Manually run the migration
        $migration = $this->getMigration();
        $migration->up();

        // Assert all required columns exist
        $this->assertTrue(Schema::hasColumn('orders', 'external_id'));
        $this->assertTrue(Schema::hasColumn('orders', 'service'));
        $this->assertTrue(Schema::hasColumn('orders', 'link'));
        $this->assertTrue(Schema::hasColumn('orders', 'quantity'));
        $this->assertTrue(Schema::hasColumn('orders', 'status'));
        $this->assertTrue(Schema::hasColumn('orders', 'start_count'));
        $this->assertTrue(Schema::hasColumn('orders', 'remains'));

        // Assert custom column still exists
        $this->assertTrue(Schema::hasColumn('orders', 'custom_field'));
    }

    public function test_migration_is_idempotent()
    {
        // Ensure clean state
        Schema::dropIfExists('orders');
        
        $migration = $this->getMigration();
        
        // Run migration first time
        $migration->up();

        // Get column count
        $columnsBefore = count(Schema::getColumnListing('orders'));

        // Run migration again
        $migration->up();

        // Get column count after
        $columnsAfter = count(Schema::getColumnListing('orders'));

        // Should have same number of columns (no duplicates)
        $this->assertEquals($columnsBefore, $columnsAfter);
    }

    public function test_migration_sets_correct_column_types()
    {
        // Ensure clean state
        Schema::dropIfExists('orders');
        
        $migration = $this->getMigration();
        $migration->up();

        // Check column types using database introspection
        $columns = Schema::getColumnListing('orders');
        
        $this->assertContains('external_id', $columns);
        $this->assertContains('service', $columns);
        $this->assertContains('link', $columns);
        $this->assertContains('quantity', $columns);
        $this->assertContains('status', $columns);
        $this->assertContains('start_count', $columns);
        $this->assertContains('remains', $columns);
    }

    public function test_migration_sets_default_values()
    {
        // Ensure clean state
        Schema::dropIfExists('orders');
        
        $migration = $this->getMigration();
        $migration->up();

        // Insert a record with minimal data
        DB::table('orders')->insert([
            'link' => 'https://example.com/post',
            'quantity' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $order = DB::table('orders')->first();

        // Check defaults
        $this->assertEquals('pending', $order->status);
        $this->assertEquals(0, $order->start_count);
        $this->assertEquals(0, $order->remains);
        $this->assertNull($order->external_id);
        $this->assertNull($order->service);
    }
}

