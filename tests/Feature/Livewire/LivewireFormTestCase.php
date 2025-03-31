<?php

namespace Tests\Feature\Livewire;

use Livewire\Livewire;

abstract class LivewireFormTestCase extends LivewireTestCase
{
    /**
     * Assert that a form validation rule works
     */
    protected function assertFormValidationRule(
        string $componentClass,
        string $fieldName,
        mixed $invalidValue,
        string $validationRule,
        array $params = []
    ): void
    {
        Livewire::actingAs($this->user)
            ->test($componentClass, $params)
            ->set($fieldName, $invalidValue)
            ->call('submit')
            ->assertHasErrors([$fieldName => $validationRule]);
    }
    
    /**
     * Assert that a form field is required
     */
    protected function assertFormFieldIsRequired(string $componentClass, string $fieldName, array $params = []): void
    {
        $this->assertFormValidationRule($componentClass, $fieldName, '', 'required', $params);
    }
    
    /**
     * Assert that an email field is validating emails
     */
    protected function assertEmailValidation(string $componentClass, string $fieldName, array $params = []): void
    {
        $this->assertFormValidationRule($componentClass, $fieldName, 'not-an-email', 'email', $params);
    }
    
    /**
     * Assert that a password field meets minimum length requirements
     */
    protected function assertPasswordMinLength(
        string $componentClass, 
        string $fieldName, 
        int $minLength = 8, 
        array $params = []
    ): void
    {
        $this->assertFormValidationRule(
            $componentClass, 
            $fieldName, 
            str_repeat('a', $minLength - 1), 
            'min', 
            $params
        );
    }
    
    /**
     * Assert that the password confirmation is required and must match
     */
    protected function assertPasswordConfirmation(
        string $componentClass, 
        string $passwordField = 'password', 
        string $confirmationField = 'password_confirmation', 
        array $params = []
    ): void
    {
        Livewire::actingAs($this->user)
            ->test($componentClass, $params)
            ->set($passwordField, 'password123')
            ->set($confirmationField, 'different-password')
            ->call('submit')
            ->assertHasErrors([$confirmationField => 'same']);
    }
    
    /**
     * Test a complete form submission
     */
    protected function assertFormSubmitsSuccessfully(
        string $componentClass,
        array $formData,
        array $expectedOutcome,
        string $submitMethod = 'submit',
        array $params = []
    ): void
    {
        $component = Livewire::actingAs($this->user)
            ->test($componentClass, $params);
            
        // Set form values
        foreach ($formData as $field => $value) {
            $component->set($field, $value);
        }
        
        // Call the submit method
        $component = $component->call($submitMethod);
        
        // Verify no validation errors
        $component->assertHasNoErrors();
        
        // Check expected outcomes (redirect, event, see text, property values)
        if (isset($expectedOutcome['redirect'])) {
            $component->assertRedirect($expectedOutcome['redirect']);
        }
        
        if (isset($expectedOutcome['event'])) {
            $component->assertEmitted($expectedOutcome['event']);
        }
        
        if (isset($expectedOutcome['see'])) {
            $component->assertSee($expectedOutcome['see']);
        }
        
        if (isset($expectedOutcome['property'])) {
            foreach ($expectedOutcome['property'] as $prop => $value) {
                $component->assertSet($prop, $value);
            }
        }
        
        if (isset($expectedOutcome['database'])) {
            foreach ($expectedOutcome['database'] as $table => $data) {
                $this->assertDatabaseHas($table, $data);
            }
        }
    }
    
    /**
     * Test that form fields get reset after submission
     */
    protected function assertFormResetsAfterSubmission(
        string $componentClass,
        array $formData,
        array $fieldsToCheck,
        string $submitMethod = 'submit',
        array $params = []
    ): void
    {
        $component = Livewire::actingAs($this->user)
            ->test($componentClass, $params);
            
        // Set form values
        foreach ($formData as $field => $value) {
            $component->set($field, $value);
        }
        
        // Call the submit method
        $component = $component->call($submitMethod);
        
        // Check that fields are reset
        foreach ($fieldsToCheck as $field) {
            if (strpos($field, '.') !== false) {
                // Handle nested properties (e.g., form.name)
                $component->assertSet($field, '');
            } else {
                $component->assertSet($field, null);
            }
        }
    }
} 