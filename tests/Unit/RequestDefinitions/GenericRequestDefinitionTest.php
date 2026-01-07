<?php

namespace Istoy\Tests\Unit\RequestDefinitions;

use Istoy\Tests\TestCase;
use Istoy\RequestDefinitions\GenericRequestDefinition;
use Istoy\RequestDefinitions\ValidationException;

class GenericRequestDefinitionTest extends TestCase
{
    public function test_validation_data_returns_payload_by_default()
    {
        $definition = new ConcreteRequestDefinition(['test' => 'value']);
        
        $validationData = $definition->getValidationData();
        
        $this->assertEquals(['test' => 'value'], $validationData);
    }

    public function test_validator_creates_validator_instance()
    {
        $definition = new ConcreteRequestDefinition(['test' => 'value']);
        
        $validator = $definition->validator();
        
        $this->assertInstanceOf(\Illuminate\Validation\Validator::class, $validator);
    }

    public function test_is_valid_returns_true_for_valid_data()
    {
        $definition = new ConcreteRequestDefinition(['test' => 'required_value']);
        
        $this->assertTrue($definition->isValid());
    }

    public function test_is_valid_returns_false_for_invalid_data()
    {
        $definition = new ConcreteRequestDefinition([]);
        
        $this->assertFalse($definition->isValid());
    }

    public function test_validate_throws_exception_for_invalid_data()
    {
        $definition = new ConcreteRequestDefinition([]);
        
        $this->expectException(ValidationException::class);
        
        $definition->validate();
    }

    public function test_validate_returns_self_for_valid_data()
    {
        $definition = new ConcreteRequestDefinition(['test' => 'required_value']);
        
        $result = $definition->validate();
        
        $this->assertInstanceOf(GenericRequestDefinition::class, $result);
    }

    public function test_errors_returns_message_bag()
    {
        $definition = new ConcreteRequestDefinition([]);
        
        $errors = $definition->errors();
        
        $this->assertInstanceOf(\Illuminate\Contracts\Support\MessageBag::class, $errors);
        $this->assertTrue($errors->has('test'));
    }

    public function test_add_options_merges_options()
    {
        $definition = new ConcreteRequestDefinition([]);
        
        $definition->addOptions(['timeout' => 30]);
        $definition->addOptions(['connect_timeout' => 10]);
        
        $allOptions = $definition->getAllOptions();
        
        $this->assertArrayHasKey('timeout', $allOptions);
        $this->assertArrayHasKey('connect_timeout', $allOptions);
        $this->assertEquals(30, $allOptions['timeout']);
        $this->assertEquals(10, $allOptions['connect_timeout']);
    }

    public function test_is_external_returns_true_by_default()
    {
        $definition = new ConcreteRequestDefinition([]);
        
        $this->assertTrue($definition->isExternal());
    }

    public function test_invoke_calls_send()
    {
        $definition = new ConcreteRequestDefinition([]);
        
        $result = $definition();
        
        $this->assertEquals('response', $result);
    }
}

// Concrete implementation for testing
class ConcreteRequestDefinition extends GenericRequestDefinition
{
    protected $testPayload;

    public function __construct($payload = [])
    {
        $this->testPayload = $payload;
    }

    public function payload(): ?array
    {
        return $this->testPayload;
    }

    public function rules(): array
    {
        return [
            'test' => 'required',
        ];
    }

    public function headers(): array
    {
        return [];
    }

    public function options(): array
    {
        return [];
    }

    public function send()
    {
        return 'response';
    }

    public function uniqueID(): string
    {
        return md5(static::class . json_encode($this->payload()));
    }

    // Helper methods for testing
    public function getValidationData()
    {
        return $this->validationData();
    }

    public function getAllOptions()
    {
        return $this->allOptions();
    }
}

