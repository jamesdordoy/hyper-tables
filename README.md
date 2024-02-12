# This is my package hyper-tables


This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require jamesdordoy/hyper-tables
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="hyper-tables-config"
```

This is the contents of the published config file:

```php
return [
    'table_path' => env('HYPER_TABLES_PATH', app_path('Tables')),
    'migration_prefix' => '',
];
```

## Usage

### Note: This package makes use of Laravels migration table to keep track of run migrations. So before attemping to migrate your hyper tables. Please run `php artisan migrate` to create this table. If your happy with Laravel's default users table then you can leave the existing migration to delete it and use HyperTables.

```php
<?php

namespace App\Tables;

use App\Models\User;
use JamesDordoy\HyperTables\Models\Table;
use JamesDordoy\HyperTables\Attributes\Migrate;

class UsersTable extends Table
{
    public function __construct()
    {
        parent::__construct(User::class);
    }

    public function up(): void
    {
        $this->table->id();
        $this->table->string('name');
        $this->table->string('email')->unique();
        $this->table->timestamp('email_verified_at')->nullable();
        $this->table->string('password');
        $this->table->rememberToken();
        $this->table->timestamps();
    }

    #[Migrate(self::class)]
    public function addLastName()
    {
        $this->table->string('last_name')->nullable()->after('name');
    }

    #[Migrate(self::class)]
    public function addActiveAt()
    {
        $this->table->dateTime('active_at')->nullable()->after('remember_token');
    }
}
```

### To run your new migrations run:
```bash
php artisan hyper-tables-migrate
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
