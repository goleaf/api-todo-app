<?php

namespace Tests\Feature\Livewire\Examples;

use App\Http\Livewire\Examples\HypervelDemo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HypervelDemoTest extends TestCase
{
    /**
     * Test that the component can be rendered
     *
     * @return void
     */
    public function test_component_can_render()
    {
        Livewire::test(HypervelDemo::class)
            ->assertStatus(200);
    }

    /**
     * Test that items are initialized correctly
     *
     * @return void
     */
    public function test_items_are_initialized()
    {
        Livewire::test(HypervelDemo::class)
            ->assertSeeHtml('Learn Livewire')
            ->assertSeeHtml('Migrate from Vue')
            ->assertSeeHtml('Master Hyperscript')
            ->assertSeeHtml('Build awesome app');
    }

    /**
     * Test adding a new item
     *
     * @return void
     */
    public function test_can_add_item()
    {
        Livewire::test(HypervelDemo::class)
            ->set('newItem', 'Test Hypervel Integration')
            ->call('addItem')
            ->assertSet('newItem', '')
            ->assertSeeHtml('Test Hypervel Integration');
    }

    /**
     * Test toggling item completion status
     *
     * @return void
     */
    public function test_can_toggle_item_complete()
    {
        Livewire::test(HypervelDemo::class)
            ->call('toggleComplete', 2)
            ->assertSeeHtml('line-through')
            ->call('toggleComplete', 2)
            ->assertDontSeeHtml('line-through');
    }

    /**
     * Test removing an item
     *
     * @return void
     */
    public function test_can_remove_item()
    {
        Livewire::test(HypervelDemo::class)
            ->call('removeItem', 3)
            ->assertDontSeeHtml('Master Hyperscript');
    }

    /**
     * Test filtering items by text
     *
     * @return void
     */
    public function test_can_filter_items_by_text()
    {
        Livewire::test(HypervelDemo::class)
            ->set('filterText', 'Livewire')
            ->assertSeeHtml('Learn Livewire')
            ->assertDontSeeHtml('Migrate from Vue')
            ->assertDontSeeHtml('Master Hyperscript');
    }

    /**
     * Test filtering completed items
     *
     * @return void
     */
    public function test_can_filter_completed_items()
    {
        Livewire::test(HypervelDemo::class)
            ->set('showCompleted', false)
            ->assertDontSeeHtml('Learn Livewire')
            ->assertSeeHtml('Migrate from Vue')
            ->assertSeeHtml('Master Hyperscript');
    }

    /**
     * Test that multiple filters can be applied simultaneously
     *
     * @return void
     */
    public function test_can_apply_multiple_filters()
    {
        $component = Livewire::test(HypervelDemo::class)
            ->set('filterText', 'Build')
            ->set('showCompleted', false);
            
        // Should see "Build awesome app" (matches text filter and is not completed)
        $component->assertSeeHtml('Build awesome app');
        
        // Make the item completed
        $component->call('toggleComplete', 4);
        
        // Now it should not be visible due to the completed filter
        $component->assertDontSeeHtml('Build awesome app');
    }
} 