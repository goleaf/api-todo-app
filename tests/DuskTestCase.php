<?php

namespace Tests;

use Illuminate\Support\Collection;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\TestCase as BaseTestCase;
use Laravel\Dusk\Browser;
use ReflectionClass;
use ReflectionMethod;
use Tests\Browser\BrowserExtensions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;
use App\Models\Category;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseMigrations;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Configure database for testing
        $this->configureDatabase();
        
        // Migrate the database
        $this->artisan('migrate:fresh', [
            '--env' => 'dusk.local',
            '--database' => 'sqlite',
            '--seed' => true,
        ]);
        
        // Create test users
        $this->createTestUsers();
        
        // Directly register each method from the BrowserExtensions trait as a macro
        $extensionMethods = (new ReflectionClass(BrowserExtensions::class))->getMethods(
            ReflectionMethod::IS_PUBLIC
        );
        
        $extensionsInstance = new class {
            use BrowserExtensions;
        };
        
        foreach ($extensionMethods as $method) {
            $methodName = $method->getName();
            $callback = function(...$parameters) use ($extensionsInstance, $methodName) {
                return $extensionsInstance->$methodName(...$parameters);
            };
            
            Browser::macro($methodName, $callback->bindTo(null, Browser::class));
        }
    }

    /**
     * Configure the database for testing.
     *
     * @return void
     */
    protected function configureDatabase(): void
    {
        // Make sure we're using the dusk.local environment
        $this->app['config']->set('database.default', 'sqlite');
        $this->app['config']->set('database.connections.sqlite.database', __DIR__ . '/../database/dusk.sqlite');
    }

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        if (! static::runningInSail()) {
            static::startChromeDriver();
        }

        // Create database file if it doesn't exist
        $databasePath = __DIR__ . '/../database/dusk.sqlite';
        if (!file_exists($databasePath)) {
            file_put_contents($databasePath, '');
        }
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions)->addArguments(collect([
            '--window-size=1920,1080',
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge([
                '--disable-gpu',
                '--headless=new',
            ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    /**
     * Determine whether the Dusk command has disabled headless mode.
     *
     * @return bool
     */
    protected function hasHeadlessDisabled(): bool
    {
        return isset($_SERVER['DUSK_HEADLESS_DISABLED']) ||
               isset($_ENV['DUSK_HEADLESS_DISABLED']);
    }

    /**
     * Determine if the browser windows should remain open.
     *
     * @return bool
     */
    protected function shouldKeepBrowserOpen(): bool
    {
        return isset($_SERVER['DUSK_KEEP_OPEN']) ||
               isset($_ENV['DUSK_KEEP_OPEN']);
    }

    /**
     * Create test users required for testing.
     *
     * @return void
     */
    protected function createTestUsers(): void
    {
        // Create an admin user if it doesn't exist
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => UserRole::ADMIN,
                'active' => true,
            ]);
        }
        
        // Create a regular user if it doesn't exist
        if (!User::where('email', 'user@example.com')->exists()) {
            $user = User::create([
                'name' => 'Regular User',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => UserRole::USER,
                'active' => true,
            ]);
        } else {
            $user = User::where('email', 'user@example.com')->first();
        }
        
        // Create a test category if it doesn't exist
        if (Category::count() === 0) {
            Category::create([
                'name' => 'Test Category',
                'user_id' => $user->id,
                'type' => 'work',
                'color' => '#3355FF'
            ]);
        }
    }
} 