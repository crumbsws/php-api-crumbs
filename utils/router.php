<?php 

class Router {

    private $routes = [];


    public function add($method, $path, $callback){
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback
        ];
    }


    public function dispatch() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        header('Content-Type: application/json');
        header("Access-Control-Allow-Credentials: true");
        header('Access-Control-Allow-Origin: http://localhost:3000'); // change before deployment
        header('Access-Control-Allow-Headers: Content-Type');
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

        $method = $_SERVER['REQUEST_METHOD'];
        foreach ($this->routes as $route) {
            if( $route['path'] == $uri && $route['method'] == $method) {

                if(is_callable($route['callback'])) {
                    return call_user_func_array($route['callback'], []);
                }
              
            }
        }
    }
}


?>