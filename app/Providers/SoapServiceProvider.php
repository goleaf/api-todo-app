<?php

namespace App\Providers;

use Artisaninweb\SoapWrapper\SoapWrapper;
use Illuminate\Support\ServiceProvider;

class SoapServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(SoapWrapper $soapWrapper): void
    {
        $this->registerSoapServices($soapWrapper);
    }

    /**
     * Register SOAP services from configuration.
     */
    protected function registerSoapServices(SoapWrapper $soapWrapper): void
    {
        $services = config('soap.services', []);

        foreach ($services as $name => $config) {
            $soapWrapper->add($name, function ($service) use ($config) {
                $service
                    ->wsdl($config['wsdl'])
                    ->trace(true);

                // Add authentication if provided
                if (!empty($config['username']) && !empty($config['password'])) {
                    $service->basicAuth($config['username'], $config['password']);
                }

                // Add custom options if provided
                if (!empty($config['options'])) {
                    $service->options($config['options']);
                }
            });
        }
    }
} 