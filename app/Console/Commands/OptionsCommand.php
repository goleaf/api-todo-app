<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Appstract\Options\Option as OptionModel;
use Appstract\Options\OptionFacade as Option;

class OptionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:options
                            {action=list : Action to perform (list, get, set, remove)}
                            {key? : Option key}
                            {value? : Option value}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage application options';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $key = $this->argument('key');
        $value = $this->argument('value');

        switch ($action) {
            case 'list':
                $this->listOptions();
                break;
            case 'get':
                $this->getOption($key);
                break;
            case 'set':
                $this->setOption($key, $value);
                break;
            case 'remove':
                $this->removeOption($key);
                break;
            default:
                $this->error("Unknown action: {$action}");
                return 1;
        }

        return 0;
    }

    /**
     * List all options.
     */
    private function listOptions()
    {
        $options = OptionModel::all();
        
        if ($options->isEmpty()) {
            $this->info('No options found.');
            return;
        }
        
        $headers = ['Key', 'Value'];
        $rows = [];
        
        foreach ($options as $option) {
            $rows[] = [$option->key, $option->value];
        }
        
        $this->table($headers, $rows);
    }

    /**
     * Get a specific option.
     *
     * @param string|null $key
     */
    private function getOption($key)
    {
        if (empty($key)) {
            $this->error('Key is required for get action.');
            return;
        }
        
        if (!Option::exists($key)) {
            $this->error("Option with key '{$key}' not found.");
            return;
        }
        
        $value = Option::get($key);
        $this->info("Option '{$key}': " . $value);
    }

    /**
     * Set an option.
     *
     * @param string|null $key
     * @param mixed|null $value
     */
    private function setOption($key, $value)
    {
        if (empty($key)) {
            $this->error('Key is required for set action.');
            return;
        }
        
        if ($value === null) {
            $value = $this->ask("Enter value for '{$key}':");
        }
        
        Option::set([$key => $value]);
        $this->info("Option '{$key}' set to '{$value}'.");
    }

    /**
     * Remove an option.
     *
     * @param string|null $key
     */
    private function removeOption($key)
    {
        if (empty($key)) {
            $this->error('Key is required for remove action.');
            return;
        }
        
        if (!Option::exists($key)) {
            $this->error("Option with key '{$key}' not found.");
            return;
        }
        
        if ($this->confirm("Are you sure you want to remove option '{$key}'?")) {
            Option::remove($key);
            $this->info("Option '{$key}' removed.");
        } else {
            $this->info('Operation cancelled.');
        }
    }
} 