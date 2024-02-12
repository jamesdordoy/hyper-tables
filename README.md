# HyperTables

## (Proof of concept package)

Ever wanted to store your migrations for a single table as a class and be able to read through all of the changes? This package allows you to create model table classes to manage your migrations. The package still uses Laravels Schema classes and migrations table to keep track of differences so no changes are needed.

This can help keep your model migration logic from becoming hard to find in what a normal migrations folder can look like on a larger project (The dumping ground!).

`php artisan migrate` still works in the same way so the package wont cause issues when trying to migrate other 3rd party packages. 

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

### Note: This package makes use of Laravel's migration table to keep track of already run migrations. So before attemping to migrate your HyperTables. Please run `php artisan migrate` to create this table. If your happy with Laravel's default users table then you can leave the existing migration or delete it and use a HyperTable.

### Note: HyperTables dosent store a migration reference for creating tables as that table has either been created or it hasnt and this is handled.

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
