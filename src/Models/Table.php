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

    public function __construct(protected Model $model)
    {
        if (! Schema::hasTable($this->model->getTable())) {
            Schema::create($this->model->getTable(), fn (Blueprint $table) => $this->up($this->table = $table));
        } else {
            Schema::table($this->model->getTable(), fn (Blueprint $table) => $this->run($this->table = $table));
        }
    }

    abstract public function up();

    public function run()
    {
        $class = new ReflectionClass($this);

        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);

        $methods = collect($methods)
            ->filter(fn (ReflectionMethod $method) => ! empty($method->getAttributes()))
            ->filter(fn (ReflectionMethod $method) => collect($method->getAttributes())->filter(fn (ReflectionAttribute $attribute) => $attribute->getName() === Migrate::class));

        $migrations = Migration::get();

        $methods->each(function (ReflectionMethod $method) use ($class, $migrations) {

            $migrationName = sprintf('%s::%s', $class->name, $method->name);

            if (empty($migrations->filter(fn (Migration $migration) => $migration->migration === $migrationName)->toArray())) {

                $this->{$method->name}();

                Migration::create([
                    'migration' => $migrationName,
                    'batch' => $migrations->last()->batch + 1,
                ]);
            }
        });
    }
}
