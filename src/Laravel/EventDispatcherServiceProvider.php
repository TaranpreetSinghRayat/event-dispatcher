<?php

namespace EventDispatcher\Laravel;

use EventDispatcher\EventDispatcher;
use EventDispatcher\Contracts\DispatcherInterface;
use Illuminate\Support\ServiceProvider;

/**
 * Laravel Service Provider for Event Dispatcher
 */
class EventDispatcherServiceProvider extends ServiceProvider
{
    /**
     * Register services
     * 
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(DispatcherInterface::class, function ($app) {
            return new EventDispatcher();
        });

        $this->app->alias(DispatcherInterface::class, 'event.dispatcher');
        $this->app->alias(DispatcherInterface::class, EventDispatcher::class);
    }

    /**
     * Bootstrap services
     * 
     * @return void
     */
    public function boot(): void
    {
        // Publish configuration if needed
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/event-dispatcher.php' => config_path('event-dispatcher.php'),
            ], 'event-dispatcher-config');
        }

        // Auto-register subscribers from config
        $this->registerSubscribers();
    }

    /**
     * Register subscribers from configuration
     * 
     * @return void
     */
    protected function registerSubscribers(): void
    {
        $subscribers = config('event-dispatcher.subscribers', []);

        if (empty($subscribers)) {
            return;
        }

        $dispatcher = $this->app->make(DispatcherInterface::class);

        foreach ($subscribers as $subscriber) {
            $dispatcher->subscribe($this->app->make($subscriber));
        }
    }

    /**
     * Get the services provided by the provider
     * 
     * @return array
     */
    public function provides(): array
    {
        return [
            DispatcherInterface::class,
            'event.dispatcher',
            EventDispatcher::class,
        ];
    }
}

