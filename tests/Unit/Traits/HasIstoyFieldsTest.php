<?php

namespace Istoy\Tests\Unit\Traits;

use Istoy\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Istoy\Traits\HasIstoyFields;

class HasIstoyFieldsTest extends TestCase
{
    public function test_trait_automatically_adds_istoy_fields_to_fillable()
    {
        $order = new TestOrderModel();
        
        $fillable = $order->getFillable();
        
        // Check all Istoy fields are in fillable
        $this->assertContains('external_id', $fillable);
        $this->assertContains('service', $fillable);
        $this->assertContains('link', $fillable);
        $this->assertContains('quantity', $fillable);
        $this->assertContains('status', $fillable);
        $this->assertContains('start_count', $fillable);
        $this->assertContains('remains', $fillable);
    }

    public function test_trait_merges_with_existing_fillable_fields()
    {
        $order = new TestOrderModelWithFields();
        
        $fillable = $order->getFillable();
        
        // Check Istoy fields are present
        $this->assertContains('external_id', $fillable);
        $this->assertContains('service', $fillable);
        
        // Check existing fields are still present
        $this->assertContains('custom_field', $fillable);
        $this->assertContains('user_id', $fillable);
    }

    public function test_trait_does_not_create_duplicates()
    {
        $order = new TestOrderModelWithDuplicateFields();
        
        $fillable = $order->getFillable();
        
        // Count occurrences of each field
        $counts = array_count_values($fillable);
        
        // Each field should appear only once
        $this->assertEquals(1, $counts['external_id'] ?? 0);
        $this->assertEquals(1, $counts['service'] ?? 0);
        $this->assertEquals(1, $counts['link'] ?? 0);
    }
}

// Test model without existing fillable
class TestOrderModel extends Model
{
    use HasIstoyFields;
    
    protected $table = 'orders';
}

// Test model with existing fillable fields
class TestOrderModelWithFields extends Model
{
    use HasIstoyFields;
    
    protected $table = 'orders';
    
    protected $fillable = [
        'custom_field',
        'user_id',
    ];
}

// Test model with some Istoy fields already in fillable
class TestOrderModelWithDuplicateFields extends Model
{
    use HasIstoyFields;
    
    protected $table = 'orders';
    
    protected $fillable = [
        'external_id',
        'service',
        'link',
        'custom_field',
    ];
}

