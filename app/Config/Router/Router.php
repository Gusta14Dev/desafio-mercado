<?php

declare(strict_types=1);

namespace App\Config\Router;

use Closure;
use Exception;
use Reflection;
use ReflectionFunction;
use ReflectionMethod;

class Router
{
    private static string $url = '';
    public static array $routes = [];
    private static Request $request;

    private static $instancia = null;

    public function __construct()
    {
        self::$request = new Request;
        self::$url = env('URL');
    }

    public static function getInstance(): Router
    {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }

    public static function getRequest(): Request
    {
        return self::$request;
    }

    private static function addRoute(string $method, string $route, array $params = []): void
    {
        $params['variables'] = [];

        $patternVariable = '/{(.*?)}/';

        if (preg_match_all($patternVariable, $route, $matches)) {
            $route = preg_replace($patternVariable, '(.*?)', $route);
            $params['variables'] = $matches[1];
        }

        foreach ($params as $key => $value) {
            if ($key == 'variables')
                continue;

            $nameController = "\\" . $key;
            $controller = new $nameController();
            $params['controller'] = $controller;
            $params['method'] = $value;

            unset($params[$key]);
            continue;
        }

        $patternRoute = '/^' . str_replace('/', '\/', $route) . '$/';

        self::$routes[$patternRoute][$method] = $params;
    }

    private static function getUri(): string
    {
        $uri = self::$request->getUri();

        if (substr($uri, -1) == '/' && strlen($uri) > 1)
            return substr($uri, 0, -1);

        return $uri ?: '/';
    }

    private static function getRoute(): array
    {
        $uri = self::getUri();

        $httpMethod = self::$request->getHttpMethod();

        foreach (self::$routes as $patternRoute => $methods) {
            if (preg_match($patternRoute, $uri, $matches)) {
                if (isset($methods[$httpMethod])) {
                    unset($matches[0]);
                    $methods[$httpMethod]['variables'] = array_combine($methods[$httpMethod]['variables'], $matches);
                    if(isset($methods[$httpMethod]['variables']['id']))
                        $methods[$httpMethod]['variables']['id'] = intval($methods[$httpMethod]['variables']['id']);

                    return $methods[$httpMethod];
                }

                throw new Exception("Método não permitido", 405);
            }
        }
        throw new Exception("URL não encontrada", 404);
    }

    public static function run(): Response
    {
        try {
            $route = self::getRoute();

            if (!isset($route['controller']) || !isset($route['method']))
                throw new Exception("URL não pode ser processada", 500);

            $controller = $route['controller'];
            $method = $route['method'];

            $reflectionFunction = new ReflectionMethod($controller::class, $method);
            $parameters = $reflectionFunction->getParameters();
            foreach ($parameters as $parameter) {
                if($parameter->getName() == "request" && (string) $parameter->getType() == Request::class)
                    $route['variables']['request'] = self::getRequest();
            }

            return $controller->$method(...$route['variables']);
        } catch (Exception $e) {
            return new Response(content: "Message: " . $e->getMessage() . " - CODE: " . $e->getCode(), httpCode: 500, contentType: 'text/html');
        }
    }

    private static function getGroup(string|null $group, string $route): string
    {
        if (!is_null($group))
            $group = '/' . preg_replace('/[^A-Za-z]/', '', $group);

        if (substr($route, -1) == '/' && (strlen($route) > 1 || !is_null($group)))
            return $group . substr($route, 0, -1);

        return $group . $route;
    }

    public static function get(string $route, array $params, string|null $group = null): void
    {
        if (!is_null($group))
            $route = self::getGroup($group, $route);
        self::addRoute('GET', $route, $params);
    }

    public static function post(string $route, array $params, string $group = null): void
    {
        if (!is_null($group))
            $route = self::getGroup($group, $route);
        self::addRoute('POST', $route, $params);
    }

    public static function put(string $route, array $params, string $group = null): void
    {
        if (!is_null($group))
            $route = self::getGroup($group, $route);
        self::addRoute('PUT', $route, $params);
    }

    public static function delete(string $route, array $params, string $group = null): void
    {
        if (!is_null($group))
            $route = self::getGroup($group, $route);
        self::addRoute('DELETE', $route, $params);
    }
}
