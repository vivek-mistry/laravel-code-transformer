<?php

namespace VivekMistry\LaravelCodeTransformer;

use Illuminate\Support\ServiceProvider;
use VivekMistry\LaravelCodeTransformer\Commands\TransformCodeVisual;

class TransformCodeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                TransformCodeVisual::class,
            ]);
            
            // $this->publishes([
            //     __DIR__.'/Stubs' => base_path('stubs/repository-generator'),
            // ], 'repository-generator-stubs');
        }
    }

    public function register()
    {
        //
    }
}