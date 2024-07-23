<?php

declare(strict_types=1); 
namespace App\Controllers;

use App\Config\DB\DB;
use App\Config\Router\Request;
use App\Config\Router\Response;
use App\Config\Validator\Validator;
use App\Models\Sale;

class SalesController {

    public function list(): Response{
        $sales = DB::select(new Sale());        
        return new Response(content: $sales);
    }

    public function show(int $id): Response{
        $sale = (new Sale())->find($id);
        return new Response(content: $sale);
    }

    public function create(Request $request): Response{
        $data = (array) $request->all();

        $validator = new Validator();

        if (!$validator->validate($data, Sale::rules())) {
            $errors = $validator->getErrors();
            return new Response(content: $errors, status: "error", httpCode: 400);
        }

        $sale = (new Sale())->create($data);

        return new Response(content: [
            'id' => $sale->id,
            'date' => $sale->date,
            'price_total' => $sale->price_total,
            'price_total_taxes' => $sale->price_total_taxes,
            'itens' => []
        ]);
    }

    public function update(int $id, Request $request): Response{
        $data = (array) $request->all();

        $validator = new Validator();

        if (!$validator->validate($data, Sale::rules())) {
            $errors = $validator->getErrors();
            return new Response(content: $errors, status: "error", httpCode: 400);
        }
        $sale = (new Sale())->update($id, $data);
        return new Response(content: $sale);
    }

    public function destroy(int $id): Response{
        if(!(new Sale())->delete($id))
            throw new Exception("Unable to delete this item");
            
        return new Response(content: []);
    }
}