<?php

declare(strict_types=1);

use App\Config\DB\DB;
use App\Config\Router\Router;

require __DIR__.'/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

Router::getInstance();
DB::getInstance();

include __DIR__.'/../routes/api.php';