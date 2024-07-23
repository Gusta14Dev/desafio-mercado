<?php

declare(strict_types=1); 
namespace App\Models;

class TypeProduct extends Model {

    protected static $tableName = 'type_products';
    protected static $relation = 'type_product';

    protected static $fillable = [
        'id',
        'name',
        'porcent_tax',
    ];

    public int $id;
    public string $name;
    public int $porcent_tax;

    public static function rules()
    {
        return [
            "name" => ["required"],
            "porcent_tax" => ["required", "integer"]
        ];
    }
}