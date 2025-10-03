<?php

namespace VivekMistry\LaravelCodeTransformer\Services;

use Illuminate\Support\Str;
use ReflectionClass;

class CodeTransformer
{
    public function transformController(string $sourceClass, string $targetModel): string
    {
        // Get the source controller code
        $sourceCode = file_get_contents($this->getClassPath($sourceClass));
        
        // Parse and transform
        return $this->transformCode($sourceCode, [
            'source_model' => $this->extractModelFromController($sourceClass),
            'target_model' => $targetModel,
            'source_controller' => $sourceClass,
            'target_controller' => $this->generateTargetControllerName($sourceClass, $targetModel)
        ]);
    }

    protected function transformCode(string $code, array $transformations): string
    {
        $transformations = array_merge($transformations, [
            'source_model_snake' => Str::snake($transformations['source_model']),
            'target_model_snake' => Str::snake($transformations['target_model']),
            'source_model_camel' => Str::camel($transformations['source_model']),
            'target_model_camel' => Str::camel($transformations['target_model']),
            'source_model_plural' => Str::plural($transformations['source_model']),
            'target_model_plural' => Str::plural($transformations['target_model']),
        ]);

        // Apply transformations
        foreach ($transformations as $key => $value) {
            $code = $this->applyTransformation($code, $key, $value, $transformations);
        }

        return $code;
    }

    protected function applyTransformation(string $code, string $type, string $value, array $all): string
    {
        return match($type) {
            'source_model' => $this->replaceModelReferences($code, $all['source_model'], $all['target_model']),
            'source_controller' => str_replace(
                $all['source_controller'], 
                $all['target_controller'], 
                $code
            ),
            'source_model_snake' => $this->replaceVariableNames($code, $all['source_model_snake'], $all['target_model_snake']),
            default => $code
        };
    }

    protected function replaceModelReferences(string $code, string $sourceModel, string $targetModel): string
    {
        // Replace class names
        $code = str_replace([
            "use App\\Models\\{$sourceModel};",
            "{$sourceModel}::",
            "{$sourceModel} ",
            "{$sourceModel},",
            "({$sourceModel} ",
            "{$sourceModel}\$"
        ], [
            "use App\\Models\\{$targetModel};",
            "{$targetModel}::", 
            "{$targetModel} ",
            "{$targetModel},",
            "({$targetModel} ",
            "{$targetModel}\$"
        ], $code);

        // Replace variable names in camelCase
        $sourceVar = Str::camel($sourceModel);
        $targetVar = Str::camel($targetModel);
        
        $code = preg_replace(
            '/\b' . $sourceVar . '\b/', 
            $targetVar, 
            $code
        );

        return $code;
    }

    protected function replaceVariableNames(string $code, string $sourceSnake, string $targetSnake): string
    {
        // Replace folder names, route names, etc.
        return str_replace($sourceSnake, $targetSnake, $code);
    }

    protected function extractModelFromController(string $controllerClass): string
    {
        // Extract "Brand" from "BrandController"
        return str_replace('Controller', '', class_basename($controllerClass));
    }

    protected function generateTargetControllerName(string $sourceController, string $targetModel): string
    {
        $namespace = (new ReflectionClass($sourceController))->getNamespaceName();
        return $namespace . '\\' . $targetModel . 'Controller';
    }
}