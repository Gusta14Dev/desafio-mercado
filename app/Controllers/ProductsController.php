<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config\DB\DB;
use App\Config\Router\Request;
use App\Config\Router\Response;
use App\Config\Validator\Validator;
use App\Models\Product;

class ProductsController
{

    public function list(): Response
    {
        $Products = DB::select(new Product());
        return new Response(content: $Products);
    }

    public function show(int $id): Response
    {
        $typeProduct = (new Product())->find($id);
        return new Response(content: $typeProduct);
    }

    public function create(Request $request): Response
    {
        $data = (array) $request->all();

        $validator = new Validator();

        if (!$validator->validate($data, Product::rules())) {
            $errors = $validator->getErrors();
            return new Response(content: $errors, status: "error", httpCode: 400);
        }
        $typeProduct = (new Product())->create($data);
        return new Response(content: [
            'id' => $typeProduct->id,
            'name' => $typeProduct->name,
            'description' => $typeProduct->description,
            'price_unity' => $typeProduct->price_unity,
            'type_id' => $typeProduct->type_id,
        ]);
    }

    public function update(int $id, Request $request): Response
    {
        $data = (array) $request->all();

        $validator = new Validator();

        if (!$validator->validate($data, Product::rules())) {
            $errors = $validator->getErrors();
            return new Response(content: $errors, status: "error", httpCode: 400);
        }
        $typeProduct = (new Product())->update($id, $data);
        return new Response(content: $typeProduct);
    }

    public function destroy(int $id): Response
    {
        if (!(new Product())->delete($id))
            throw new Exception("Unable to delete this item");

        return new Response(content: []);
    }
}
