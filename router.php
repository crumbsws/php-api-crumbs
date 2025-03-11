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


    public function dispatch($uri, $method) {
        foreach ($this->routes as $uri => $callback) {
            if( $url == $_SERVER['REQUEST_URI'] ){

                if(is_callable($callback)) {
                    return call_user_func_array($callback);
                };
            }
        }
    }
}


?>