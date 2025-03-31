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

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Boot the browser extension trait.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        
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
} 