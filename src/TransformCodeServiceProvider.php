<?php

namespace VivekMistry\LaravelCodeTransformer;

use Illuminate\Support\ServiceProvider;
use VivekMistry\LaravelCodeTransformer\Commands\TransformCode;

class TransformCodeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                TransformCode::class,
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