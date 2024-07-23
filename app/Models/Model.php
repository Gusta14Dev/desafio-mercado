<?php

declare(strict_types=1);

namespace App\Models;

use App\Config\DB\DB;
use Exception;

abstract class Model
{

    protected static $tableName;
    protected static $relation;

    public static function getTableName(): string
    {
        return static::$tableName ?? strtolower(class_basename(static::class));
    }

    public static function getRelation(): string
    {
        return static::$relation;
    }

    public static function rules()
    {
        return [];
    }

    protected static $fillable = [];

    public static function getFillable(): array
    {
        return static::$fillable;
    }

    public function __construct(array $data = [])
    {
        $this->hydrate($data);
    }

    public function hydrate(array $data): self
    {
        foreach ($data as $key => $value) {
            if (in_array($key, static::getFillable())) {
                $this->$key = $value;
            }
        }

        return $this;
    }

    public function extractData(): array
    {
        $data = [];
        foreach (static::getFillable() as $field) {
            $data[$field] = $this->$field;
        }
        return $data;
    }

    public function getRelationships(): array
    {
        return [];
    }

    public function loadRelationships(array $data): self
    {
        foreach ($data as $relationshipName => $relationshipData) {
            $relationshipClass = $this->getRelationshipClass($relationshipName);
            $this->$relationshipName = new $relationshipClass($relationshipData);
        }

        return $this;
    }

    private function getRelationshipClass(string $relationshipName): string
    {
        $relationships = $this->getRelationships();
        if (!isset($relationships[$relationshipName])) {
            throw new Exception("Relationship '$relationshipName' not defined in model " . get_class($this));
        }

        return $relationships[$relationshipName];
    }

    public function delete(int $id): bool
    {
        return DB::delete(model: $this, where: ['id' => $id]);
    }

    public function update(int $id, array $data): self
    {
        DB::update(model: $this, data: $data, where: ['id' => $id]);
        return $this->find($id);
    }

    public function create(array $data): self
    {
        $id = DB::create($this, $data);
        return $this->find($id);
    }

    public function find(int $id): self
    {
        $data = DB::select(model: $this, where: ['id' => $id], limit: 1);
        return $this->hydrate($data);
    }
}
