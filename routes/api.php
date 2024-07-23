<?php

declare(strict_types=1);

use App\Config\Router\Router;
use App\Controllers\ProductsController;
use App\Controllers\SalesController;
use App\Controllers\TypesProductsController;

// ------------------VENDAS------------------

Router::get(route: '/vendas', params: [SalesController::class => 'list'], group: 'api');
Router::get(route: '/venda/{id}', params: [SalesController::class => 'show'], group: 'api');
Router::post(route: '/venda/cadastrar', params: [SalesController::class => 'create'], group: 'api');
Router::put(route: '/venda/editar/{id}', params: [SalesController::class => 'update'], group: 'api');
Router::delete(route: '/venda/deletar/{id}', params: [SalesController::class => 'destroy'], group: 'api');

// // ------------------PRODUTOS------------------

Router::get(route: '/produtos', params: [ProductsController::class => 'list'], group: 'api');
Router::get(route: '/produto/{id}', params: [ProductsController::class => 'show'], group: 'api');
Router::post(route: '/produto/cadastrar', params: [ProductsController::class => 'create'], group: 'api');
Router::put(route: '/produto/editar/{id}', params: [ProductsController::class => 'update'], group: 'api');
Router::delete(route: '/produto/deletar/{id}', params: [ProductsController::class => 'destroy'], group: 'api');

// // --------------TIPOS DE PRODUTOS--------------

Router::get(route: '/tipos-produtos', params: [TypesProductsController::class => 'list'], group: 'api');
Router::get(route: '/tipo-produto/visualizar/{id}', params: [TypesProductsController::class => 'show'], group: 'api');
Router::post(route: '/tipo-produto/cadastrar', params: [TypesProductsController::class => 'create'], group: 'api');
Router::put(route: '/tipo-produto/editar/{id}', params: [TypesProductsController::class => 'update'], group: 'api');
Router::delete(route: '/tipo-produto/deletar/{id}', params: [TypesProductsController::class => 'destroy'], group: 'api');

Router::run()->send();
