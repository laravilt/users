![Users](./arts/screenshot.jpg)

# Users Plugin for Laravilt

[![Latest Stable Version](https://poser.pugx.org/laravilt/users/version.svg)](https://packagist.org/packages/laravilt/users)
[![License](https://poser.pugx.org/laravilt/users/license.svg)](https://packagist.org/packages/laravilt/users)
[![Downloads](https://poser.pugx.org/laravilt/users/d/total.svg)](https://packagist.org/packages/laravilt/users)
[![Dependabot Updates](https://github.com/laravilt/users/actions/workflows/dependabot/dependabot-updates/badge.svg)](https://github.com/laravilt/users/actions/workflows/dependabot/dependabot-updates)
[![PHP Code Styling](https://github.com/laravilt/users/actions/workflows/fix-php-code-styling.yml/badge.svg)](https://github.com/laravilt/users/actions/workflows/fix-php-code-styling.yml)
[![Tests](https://github.com/laravilt/users/actions/workflows/tests.yml/badge.svg)](https://github.com/laravilt/users/actions/workflows/tests.yml)

Users plugin for Laravilt

## Installation

You can install the plugin via composer:

```bash
composer require laravilt/users
```

The package will automatically register its service provider which handles all Laravel-specific functionality (views, migrations, config, etc.).

## Usage

Register the plugin in your Filament panel provider:

```php
use Laravilt\Users\UsersPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(new UsersPlugin());
}
```
## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag="users-config"
```

## Assets

Publish the plugin assets:

```bash
php artisan vendor:publish --tag="users-assets"
```

## Testing

```bash
composer test
```

## Code Style

```bash
composer format
```

## Static Analysis

```bash
composer analyse
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
