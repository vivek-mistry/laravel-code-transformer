<?php

namespace VivekMistry\LaravelCodeTransformer\Commands;

use Illuminate\Console\Command;
use VivekMistry\LaravelCodeTransformer\Services\CodeTransformer;

class TransformCodeVisual extends Command
{
    protected $signature = 'transform:code {source : Source controller class or file path} {target-model : Target model name} {--output= : Output file path} {--dry-run : Show output without saving}';

    protected $description = 'Transform controller code for different models';

    public function handle(CodeTransformer $transformer)
    {
        $source = $this->argument('source');
        $targetModel = $this->argument('target-model');

        if($source == '') {
            $this->error('Source controller class or file path is required');
            return;
        }

        if($source == '--default')
        {
            $source = 'VivekMistry\LaravelCodeTransformer\app\Http\BasicController';
        }
        
        try {
            $transformedCode = $transformer->transformController($source, $targetModel);
            
            if ($this->option('dry-run')) {
                $this->info("=== TRANSFORMED CODE ===");
                $this->line($transformedCode);
                return;
            }

            $outputPath = $this->option('output') ?? $this->getDefaultOutputPath($source, $targetModel);
            
            file_put_contents($outputPath, $transformedCode);
            
            $this->info("✅ Successfully transformed code!");
            $this->line("Output: {$outputPath}");
            
        } catch (\Exception $e) {
            $this->error("Failed to transform code: {$e->getMessage()}");
        }
    }

    protected function getDefaultOutputPath(string $source, string $targetModel): string
    {
        $sourcePath = $this->getClassPath($source);
        $dir = dirname($sourcePath);
        
        return $dir . '/' . $targetModel . 'Controller.php';
    }
}