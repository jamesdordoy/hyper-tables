<?php

namespace JamesDordoy\HyperTables\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use JamesDordoy\HyperTables\Attributes\Migrate;
use JamesDordoy\HyperTables\Contracts\FollowsSchema;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

abstract class Table implements FollowsSchema
{
    protected $table;

    protected $model;

    protected bool $justCreated = false;

    protected $migrations = null;

    const FORMAT = '%s::%s';

    public function __construct(protected string $modelClass)
    {
        $this->model = resolve($modelClass);
    }

    abstract public function up(): void;

    public function createOrUpdate()
    {
        $this->migrations = Migration::get();

        if (! $this->created()) {
            $this->create();
        } else {
            $this->table();
        }
    }

    public function create(): void
    {
        Schema::create($this->model->getTable(), fn (Blueprint $table) => $this->up($this->table = $table));

        $class = new ReflectionClass($this);
        $migrationName = sprintf('%s::%s', $class->name, 'create');

        Migration::create([
            'migration' => $migrationName,
            'batch' => $this->migrations->last() ? $this->migrations->last()->batch + 1 : 1,
        ]);

        $this->justCreated = true;

        $this->table();
    }

    public function table(): void
    {
        Schema::table($this->model->getTable(), fn (Blueprint $table) => $this->run($this->table = $table));
    }

    public function drop(): void
    {
        Schema::drop($this->model->getTable());
    }

    public function rename(string $from, string $to): void
    {
        Schema::rename($from, $to);
    }

    public function run(): void
    {
        $class = new ReflectionClass($this);
        $methods = self::getMigratiableMethods();

        $methods->each(function (ReflectionMethod $method) use ($class) {

            $migrationName = self::formatMigrationName($class->name, $method->name);

            if ($this->migrations->filter(fn (Migration $migration) => $migration->migration === $migrationName)->isEmpty()) {

                $this->{$method->name}();

                Migration::create([
                    'migration' => $migrationName,
                    'batch' => $this->migrations->last() ? $this->migrations->last()->batch + 1 : 1,
                ]);
            }
        });
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function hasMigrationRun($migrationName)
    {
        if (is_null($this->migrations)) {
            $this->migrations = Migration::get();
        }

        return ! $this->migrations->filter(fn (Migration $migration) => $migration->migration === $migrationName)->isEmpty();
    }

    public function hasMigrations()
    {
        $class = new ReflectionClass($this);
        $methods = self::getMigratiableMethods();

        $methods->each(function (ReflectionMethod $method) use ($class) {

            $migrationName = self::formatMigrationName($class->name, $method->name);

            if ($this->migrations->filter(fn (Migration $migration) => $migration->migration === $migrationName)->isEmpty()) {

                return true;
            }
        });

        return false;
    }

    public function getJustCreated(): bool
    {
        return $this->justCreated;
    }

    public function created()
    {
        $class = new ReflectionClass(get_called_class());

        return $this->hasMigrationRun(self::formatMigrationName($class->name));
    }

    protected static function getMigratiableMethods()
    {
        $class = new ReflectionClass(get_called_class());

        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);

        $methods = collect($methods)
            ->filter(fn (ReflectionMethod $method) => ! collect($method->getAttributes())->isEmpty())
            ->filter(fn (ReflectionMethod $method) => collect($method->getAttributes())->filter(fn (ReflectionAttribute $attribute) => $attribute->getName() === Migrate::class));

        return $methods;
    }

    public static function getOutstandingMigrations()
    {
        $class = new ReflectionClass(get_called_class());
        $methods = self::getMigratiableMethods();
        $migrations = Migration::get();

        $methodNames = $methods->map(fn (ReflectionMethod $method) => self::formatMigrationName($class->name, $method->name));

        return $methodNames->diff($migrations->pluck('migration'));
    }

    protected static function formatMigrationName(string $class, string $method = 'create'): string
    {
        return sprintf(self::FORMAT, $class, $method);
    }
}
