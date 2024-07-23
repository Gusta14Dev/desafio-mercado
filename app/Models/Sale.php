<?php

declare(strict_types=1);

namespace App\Models;

class Sale extends Model
{
    protected static $tableName = 'sales';
    protected static $relation = 'sale';

    protected static $fillable = [
        'id',
        'date',
        'price_total',
        'price_total_taxes',
    ];

    public static function rules()
    {
        return [
            "date" => ["required", "date:Y-m-d"],
            "price_total" => ["required", "float"],
            "price_total_taxes" => ["required", "float"]
        ];
    }

    public int $id;
    public string $date;
    public float $price_total;
    public float $price_total_taxes;
}
