<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config\DB\DB;
use App\Config\Router\Request;
use App\Config\Router\Response;
use App\Config\Router\Router;
use App\Config\Validator\Validator;
use App\Models\TypeProduct;

class TypesProductsController
{

    public function list(): Response
    {
        $typeProducts = DB::select(new TypeProduct());
        return new Response(content: $typeProducts);
    }

    public function show(int $id): Response
    {
        $typeProduct = (new TypeProduct())->find($id);
        return new Response(content: $typeProduct);
    }

    public function create(Request $request): Response
    {
        $data = (array) $request->all();
        $validator = new Validator();

        if (!$validator->validate($data, TypeProduct::rules())) {
            $errors = $validator->getErrors();
            return new Response(content: $errors, status: "error", httpCode: 400);
        }
        $typeProduct = (new TypeProduct())->create($data);
        return new Response(content: ['id' => $typeProduct->id, 'name' => $typeProduct->name, 'porcent_tax' => $typeProduct->porcent_tax]);
    }

    public function update(int $id, Request $request): Response
    {
        $data = (array) $request->all();

        $validator = new Validator();

        if (!$validator->validate($data, TypeProduct::rules())) {
            $errors = $validator->getErrors();
            return new Response(content: $errors, status: "error", httpCode: 400);
        }
        $typeProduct = (new TypeProduct())->update($id, $data);
        return new Response(content: $typeProduct);
    }

    public function destroy(int $id): Response
    {
        if (!(new TypeProduct())->delete($id))
            throw new Exception("Unable to delete this item");

        return new Response(content: []);
    }
}
