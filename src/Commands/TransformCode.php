<?php

namespace VivekMistry\LaravelCodeTransformer\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class TransformCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:transform-code 
        {--from=Basic : Source controller name (without Controller suffix)} 
        {--to= : Destination controller name (without Controller suffix)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone and transform CRUD controller (e.g., BasicController → CategoryController)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $fs = new Filesystem();

        $from = $this->option('from') ?: $this->ask('Enter source controller name (default: Basic)', 'Basic');
        $to = $this->option('to') ?: $this->ask('Enter new controller name (e.g., Category)');

        if (!$to) {
            $this->error('❌ Destination name is required.');
            return Command::FAILURE;
        }

        if($from == 'Basic')
        {
            $fromController = __DIR__.'/../app/Http/BasicController.php';
        }else{
            $fromController = app_path("Http/Controllers/{$from}Controller.php");
        }
        

        if (!$fs->exists($fromController)) {
            $this->warn("⚠️ Controller not found at {$fromController}");
            if ($this->confirm("Do you want to use default 'BasicController'?", true)) {
                $fromController = __DIR__.'/../app/Http/BasicController.php';
            } else {
                $this->error('❌ Operation cancelled.');
                return Command::FAILURE;
            }
        }else{
            $this->info("✅ Controller found at {$fromController}");
        }

        $content = $fs->get($fromController);

        // Replace every case
        $replaced = str_replace(
            [$from, Str::lower($from), Str::upper($from)],
            [$to, Str::lower($to), Str::upper($to)],
            $content
        );

        $newFile = app_path("Http/Controllers/{$to}Controller.php");

        if ($fs->exists($newFile) && !$this->confirm("⚠️ {$to}Controller already exists. Overwrite?", false)) {
            $this->info('❌ Operation cancelled.');
            return Command::FAILURE;
        }

        $fs->put($newFile, $replaced);

        $this->info("✅ Successfully created: {$to}Controller");
        $this->line("📂 Path: {$newFile}");

        return Command::SUCCESS;
    }
}
