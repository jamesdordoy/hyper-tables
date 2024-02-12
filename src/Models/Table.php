<?php

namespace JamesDordoy\HyperTables\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use JamesDordoy\HyperTables\Attributes\Migrate;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

abstract class Table
{
    protected $table;

    protected $model;

    public function __construct(protected string $modelClass)
    {
        $this->model = resolve($modelClass);

        if (! Schema::hasTable($this->model->getTable())) {
            \Log::info('hit datatbase for creates');
            Schema::create($this->model->getTable(), fn (Blueprint $table) => $this->up($this->table = $table));
            Schema::table($this->model->getTable(), fn (Blueprint $table) => $this->run($this->table = $table));
        } else {
            \Log::info('hit datatbase for edits');
            Schema::table($this->model->getTable(), fn (Blueprint $table) => $this->run($this->table = $table));
        }
    }

    abstract public function up(): void;

    public function run(): void
    {
        // $migrations = Migration::get(); -- Look at caching/storing these on the class as they as going to get loaded more than once....
        $class = new ReflectionClass($this);
        $methods = self::getMigratiableMethods();

        if (! $methods->isEmpty()) {
            $migrations = Migration::get();

            $methods->each(function (ReflectionMethod $method) use ($class, $migrations) {

                $migrationName = sprintf('%s::%s', $class->name, $method->name);

                if ($migrations->filter(fn (Migration $migration) => $migration->migration === $migrationName)->isEmpty()) {

                    $this->{$method->name}();

                    Migration::create([
                        'migration' => $migrationName,
                        'batch' => $migrations->last() ? $migrations->last()->batch + 1 : 1,
                    ]);
                }
            });
        }
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public static function getOutstandingMigrations()
    {
        $class = new ReflectionClass(get_called_class());
        $methods = self::getMigratiableMethods();
        $migrations = Migration::get();

        $methodNames = $methods->map(fn (ReflectionMethod $method) => sprintf('%s::%s', $class->name, $method->name));

        return $methodNames->diff($migrations->pluck('migration'));
    }

    public function hasMigrationRun($migrationName, $migrations = null)
    {
        if (is_null($migrations)) {
            $migrations = Migration::get();
        }

        return ! $migrations->filter(fn (Migration $migration) => $migration->migration === $migrationName)->isEmpty();
    }

    public function hasMigrations()
    {
        $class = new ReflectionClass($this);
        $methods = self::getMigratiableMethods();

        if (! $methods->isEmpty()) {
            $migrations = Migration::get();

            $methods->each(function (ReflectionMethod $method) use ($class, $migrations) {

                $migrationName = sprintf('%s::%s', $class->name, $method->name);

                if ($migrations->filter(fn (Migration $migration) => $migration->migration === $migrationName)->isEmpty()) {

                    return true;
                }
            });

            return false;
        }
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
}
