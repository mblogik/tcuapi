<?php

namespace MBLogik\TCUAPIClient\Tests\Unit\Models;

use MBLogik\TCUAPIClient\Tests\TestCase;
use MBLogik\TCUAPIClient\Models\BaseModel;

class TestModel extends BaseModel
{
    protected array $fillable = ['name', 'email', 'age', 'active'];
    protected array $required = ['name', 'email'];
    protected array $casts = [
        'age' => 'integer',
        'active' => 'boolean'
    ];
    
    protected function customValidation(): array
    {
        $errors = [];
        
        if ($this->get('age') && $this->get('age') < 18) {
            $errors[] = 'Age must be at least 18';
        }
        
        return $errors;
    }
}

class BaseModelTest extends TestCase
{
    public function testCanCreateModelWithData()
    {
        $model = new TestModel([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'age' => 25,
            'active' => true
        ]);
        
        $this->assertEquals('John Doe', $model->get('name'));
        $this->assertEquals('john@example.com', $model->get('email'));
        $this->assertEquals(25, $model->get('age'));
        $this->assertTrue($model->get('active'));
    }
    
    public function testCanSetAndGetValues()
    {
        $model = new TestModel();
        
        $model->set('name', 'Jane Doe');
        $model->set('email', 'jane@example.com');
        
        $this->assertEquals('Jane Doe', $model->get('name'));
        $this->assertEquals('jane@example.com', $model->get('email'));
    }
    
    public function testMagicGettersAndSetters()
    {
        $model = new TestModel();
        
        $model->name = 'Magic Name';
        $model->email = 'magic@example.com';
        
        $this->assertEquals('Magic Name', $model->name);
        $this->assertEquals('magic@example.com', $model->email);
        $this->assertTrue(isset($model->name));
        $this->assertFalse(isset($model->nonexistent));
    }
    
    public function testHasMethod()
    {
        $model = new TestModel(['name' => 'John']);
        
        $this->assertTrue($model->has('name'));
        $this->assertFalse($model->has('email'));
    }
    
    public function testFillMethod()
    {
        $model = new TestModel();
        
        $model->fill([
            'name' => 'Filled Name',
            'email' => 'filled@example.com',
            'age' => 30
        ]);
        
        $this->assertEquals('Filled Name', $model->get('name'));
        $this->assertEquals('filled@example.com', $model->get('email'));
        $this->assertEquals(30, $model->get('age'));
    }
    
    public function testToArrayMethod()
    {
        $data = [
            'name' => 'Array Test',
            'email' => 'array@example.com',
            'age' => 35,
            'active' => false
        ];
        
        $model = new TestModel($data);
        
        $this->assertEquals($data, $model->toArray());
    }
    
    public function testToJsonMethod()
    {
        $data = [
            'name' => 'JSON Test',
            'email' => 'json@example.com',
            'age' => 40,
            'active' => true
        ];
        
        $model = new TestModel($data);
        
        $this->assertEquals(json_encode($data), $model->toJson());
    }
    
    public function testCastingWorks()
    {
        $model = new TestModel([
            'age' => '25',
            'active' => 'true'
        ]);
        
        $this->assertIsInt($model->get('age'));
        $this->assertEquals(25, $model->get('age'));
        $this->assertIsBool($model->get('active'));
        $this->assertTrue($model->get('active'));
    }
    
    public function testValidationWithRequiredFields()
    {
        $model = new TestModel(['name' => 'John']);
        
        $errors = $model->validate();
        $this->assertContains('Field \'email\' is required', $errors);
    }
    
    public function testValidationWithCustomValidation()
    {
        $model = new TestModel([
            'name' => 'John',
            'email' => 'john@example.com',
            'age' => 16
        ]);
        
        $errors = $model->validate();
        $this->assertContains('Age must be at least 18', $errors);
    }
    
    public function testValidationPassesWithValidData()
    {
        $model = new TestModel([
            'name' => 'John',
            'email' => 'john@example.com',
            'age' => 25
        ]);
        
        $errors = $model->validate();
        $this->assertEmpty($errors);
    }
    
    public function testFillableRestrictsFields()
    {
        $model = new TestModel([
            'name' => 'John',
            'email' => 'john@example.com',
            'restricted_field' => 'should not be set'
        ]);
        
        $this->assertEquals('John', $model->get('name'));
        $this->assertEquals('john@example.com', $model->get('email'));
        $this->assertNull($model->get('restricted_field'));
    }
    
    public function testGetWithDefault()
    {
        $model = new TestModel();
        
        $this->assertNull($model->get('nonexistent'));
        $this->assertEquals('default', $model->get('nonexistent', 'default'));
    }
}