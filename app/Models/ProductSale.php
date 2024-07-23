<?php

declare(strict_types=1);

namespace App\Models;

class ProductSale extends Model
{

    protected static $tableName = 'product_sale';

    protected static $fillable = [
        'id',
        'sale_id',
        'product_id',
        'amount',
        'price_unity',
        'price_total',
        'tax',
    ];

    public int $id;
    public int $sale_id;
    public int $product_id;
    public int $amount;
    public float $price_unity;
    public float $price_total;
    public float $tax_total;

    public static function rules()
    {
        return [
            "sale_id" => ["required", "integer"],
            "product_id" => ["required", "integer"],
            "amount" => ["required", "integer"],
            "price_total" => ["required", "float"],
            "price_unity" => ["required", "float"],
            "tax_total" => ["required", "float"]
        ];
    }

    public function getRelationships(): array
    {
        return [
            'product' => Product::class,
            'sale' => Sale::class,
        ];
    }

    public function loadRelationships(array $data): self
    {
        parent::loadRelationships($data);

        if (isset($data['product'])) {
            $this->tipo = array_map(function ($productData) {
                return new Product($productData);
            }, $data['product']);
        }

        if (isset($data['sale'])) {
            $this->tipo = array_map(function ($saleData) {
                return new Sale($saleData);
            }, $data['sale']);
        }

        return $this;
    }
}
