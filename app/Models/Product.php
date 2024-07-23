<?php

declare(strict_types=1);

namespace App\Models;

class Product extends Model
{

    protected static $tableName = 'products';
    protected static $relation = 'product';

    protected static $fillable = [
        'name',
        'description',
        'price_unity',
        'type_id',
    ];

    public int $id;
    public string $name;
    public string $description;
    public float $price_unity;
    public int $type_id;

    public static function rules()
    {
        return [
            "name" => ["required"],
            "description" => ["required"],
            "price_unity" => ["required", "float"],
            "type_id" => ["required", "integer"]
        ];
    }

    public function getRelationships(): array
    {
        return [
            'tipo' => TypeProduct::class
        ];
    }

    public function loadRelationships(array $data): self
    {
        parent::loadRelationships($data);

        if (isset($data['tipo'])) {
            $this->tipo = array_map(function ($tipoData) {
                return new TypeProduct($tipoData);
            }, $data['tipo']);
        }

        return $this;
    }
}
