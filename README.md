## Laravel Code Transformer

A developer-friendly Artisan command that allows you to quickly clone and transform any CRUD controller in your Laravel project — without using external packages.

It copies an existing controller (e.g., BasicController, CategoryController) and automatically replaces all class names, variable names, and related references with your new model name (e.g., Service, Product).

## 🛠️ Installation

Install the package via Composer:
```bash
composer require vivek-mistry/laravel-code-transformer
```

## Execute via
```bash
php artisan transform:code
```

| Option    | Description                                               |   Default     |
| --------- | --------------------------------------------------------- | ----------- |
| `--from=` | Source controller name (without `Controller` suffix)      | `Basic`     |
| `--to=`   | Destination controller name (without `Controller` suffix) | *(required)* |


🧰 Notes
<ul>
    <li>Ideal for repetitive CRUD scaffolding in Laravel projects.</li>
    <li>Works for any existing controller in app/Http/Controllers/.</li>
    <li>Prompts before overwriting existing files.</li>
    <li>Fully local — no external package required.</li>
</ul>

## Credits

- [Vivek Mistry](https://github.com/vivek-mistry) - Project creator and maintainer

## License
MIT License. See [LICENSE](https://github.com/vivek-mistry/laravel-code-transformer/blob/DEV/LICENSE) for details.