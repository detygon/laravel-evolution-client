<?php
// src/EvolutionServiceProvider.php

namespace SamuelTerra22\LaravelEvolutionClient;

use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\ServiceProvider;
use SamuelTerra22\LaravelEvolutionClient\Notifications\Channels\EvolutionWhatsAppChannel;
use SamuelTerra22\LaravelEvolutionClient\Services\EvolutionService;

class EvolutionServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/evolution.php',
            'evolution'
        );

        // Register the main class to use with the facade
        $this->app->singleton('evolution', function ($app) {
            return new EvolutionApiClient(
                new EvolutionService(
                    config('evolution.base_url'),
                    config('evolution.api_key'),
                    config('evolution.timeout')
                ),
                config('evolution.default_instance')
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/evolution.php' => config_path('evolution.php'),
        ], 'evolution-config');

        $this->app->make(ChannelManager::class)->extend('evolution-whatsapp', function () {
            return new EvolutionWhatsAppChannel();
        });

        // Register commands if we're in console
        if ($this->app->runningInConsole()) {
            // $this->commands([
            //     // Register commands here in the future
            // ]);
        }
    }
}
