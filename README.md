# Istoy

A Laravel package for managing social media service providers (SMM) with an extensible provider architecture. This package allows you to integrate multiple social media service providers into your Laravel application.

## Features

- **Extensible Provider Architecture**: Easily add new service providers by extending the `AbstractProvider` class
- **SMM Provider Included**: Ready-to-use SMM (Social Media Marketing) provider for services like likes, views, and comments
- **Order Management**: Built-in `OrderService` for managing orders across providers
- **Request Definitions**: Structured HTTP request handling with validation
- **Status Synchronization**: Sync order statuses between your application and external providers

## Requirements

- PHP ^8.0.2
- Composer
- Laravel ^9.0|^10.0|^11.0

## Installation

### Installing PHP and Composer (macOS)

If you don't have PHP and Composer installed:

**Using Homebrew:**
```bash
# Install Homebrew if you don't have it
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install PHP
brew install php

# Install Composer
brew install composer
```

**Or download Composer directly:**
```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer
```

### Via Composer

```bash
composer require rexatgithub/istoy
```

### Publish Configuration

```bash
php artisan vendor:publish --tag=istoy-config
```

This will publish the configuration file to `config/istoy.php`.

## Configuration

### Environment Variables

Add these to your `.env` file:

```env
SMM_PROVIDER_HOST=https://smmlite.com/api/v2
SMM_PROVIDER_KEY=your_api_key_here
ISTOY_ORDER_MODEL=App\Models\Order
```

### Order Model Setup

Your Order model should implement the following:

1. Have these fillable attributes:
   - `external_id` (string/int) - ID from the external provider
   - `service` (int) - Service ID
   - `link` (string) - URL to the social media post
   - `quantity` (int) - Quantity of services to order
   - `status` (enum) - Order status
   - `start_count` (int) - Starting count
   - `remains` (int) - Remaining count

2. Implement a scope for finding by external ID:

```php
use Illuminate\Database\Eloquent\Builder;

public function scopeWithExternalId(Builder $query, $externalId): Builder
{
    return $query->where(['external_id' => $externalId]);
}
```

3. Optionally implement the `OrderContract` interface:

```php
use Istoy\Contracts\OrderContract;

class Order extends Model implements OrderContract
{
    // Your model code
}
```

## Usage

### Starting an Order

```php
use Istoy\Services\OrderService;
use App\Models\Order;

$order = Order::find(1);
$orderService = new OrderService($order);

// Start the order (pushes to provider)
$success = $orderService->start();

// Start with interval (in minutes)
$success = $orderService->start(interval: 5);
```

### Syncing Order Statuses

```php
use Istoy\Services\OrderService;

$orderService = new OrderService($order);
$orderService->syncStatuses();
```

### Using Providers Directly

```php
use Istoy\Providers\Factory;
use App\Models\Order;

$order = Order::find(1);
$provider = Factory::create($order);

// Add order to provider
$provider->add();

// Add with interval
$provider->add(interval: 10);

// Check statuses
$provider->statuses();
```

### Using SMM Provider Directly

```php
use Istoy\Providers\Smm\Service;
use App\Models\Order;

$order = Order::find(1);
$smmService = new Service($order);

$smmService->add(interval: 5);
$smmService->statuses();
```

## Available Services

The package includes the following service types:

- `HighQualityLikes` - High-quality likes service
- `PremiumQualityLikes` - Premium-quality likes service
- `HighQualityViews` - High-quality views service
- `PremiumQualityViews` - Premium-quality views service
- `Comments` - Comments service

### Using Services Enum

```php
use Istoy\Models\Enums\Services;

// Get service ID
$serviceId = Services::HighQualityLikes->id();

// Get minimum quantity
$min = Services::HighQualityLikes->minimum();

// Get maximum quantity
$max = Services::HighQualityLikes->maximum();

// Get service description
$description = Services::HighQualityLikes->description();

// Get all likes services
$likesServices = Services::likes();

// Get all views services
$viewsServices = Services::views();
```

## Creating Custom Providers

To create a custom provider:

1. Extend the `AbstractProvider` class:

```php
namespace App\Providers\Custom;

use Istoy\Providers\AbstractProvider;
use Istoy\Providers\Factory;

class CustomProvider extends AbstractProvider
{
    public function getId(): int
    {
        return 2; // Your provider ID
    }

    public function add(?int $interval = null): void
    {
        // Implement your add logic
    }

    public function statuses(): void
    {
        // Implement your status check logic
    }
}
```

2. Register your provider:

```php
use Istoy\Providers\Factory;
use App\Providers\Custom\CustomProvider;

Factory::register(2, CustomProvider::class);
```

3. Use it:

```php
$provider = Factory::create($order, providerId: 2);
```

## Order Statuses

The package includes the following order statuses:

- `Pending` - Order is pending
- `InProgress` - Order is in progress
- `Cancelled` - Order is cancelled
- `Completed` - Order is completed
- `Paused` - Order is paused

## Request Definitions

The package uses a request definition pattern for HTTP requests. You can create custom request definitions by extending `RequestDefinition`:

```php
use Istoy\RequestDefinitions\RequestDefinition;

class CustomRequest extends RequestDefinition
{
    public function method(): string
    {
        return self::HTTP_POST;
    }

    public function url(): string
    {
        return 'https://api.example.com/endpoint';
    }

    public function payload(): ?array
    {
        return ['key' => 'value'];
    }

    public function rules(): array
    {
        return ['key' => 'required'];
    }

    public function headers(): array
    {
        return ['Content-Type' => 'application/json'];
    }

    public function options(): array
    {
        return [];
    }

    public function uniqueID(): string
    {
        return md5(static::class . json_encode($this->payload()));
    }
}
```

## Testing

The package includes a comprehensive test suite using PHPUnit and Orchestra Testbench.

### Setup for Testing

**Important:** You must install dependencies before running tests.

First, install development dependencies:

```bash
composer install
```

### Running Tests

Once dependencies are installed, you can run tests:

```bash
# Run all tests (recommended)
composer test

# Or run PHPUnit directly
vendor/bin/phpunit

# Run tests with coverage
composer test-coverage

# Or run PHPUnit with coverage directly
vendor/bin/phpunit --coverage-html coverage
```

### Test Structure

The test suite includes:

- **Unit Tests**: Tests for individual components
  - Enums (Services, OrderStatuses, Statuses)
  - Factory pattern for provider creation
  - OrderService for order management
  - SMM Service provider
  - Request definitions (Add, Status)
  - Generic request definition base classes

### Test Coverage

The tests cover:
- Provider registration and factory pattern
- Order creation and status synchronization
- Request definition validation
- Enum functionality and mappings
- Error handling and edge cases

## License

MIT

## Support

For issues and feature requests, please use the GitHub issue tracker.

