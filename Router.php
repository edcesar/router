<?php
namespace NewDay\Route;

class Router
{
    private $routes;
    private $path;
    
    private $actualPathConfi;
    
    public function __construct()
    {
        $this->path = $_SERVER['REQUEST_URI'] ?? null;
        
        $this->routes['start'] = function(){};
        $this->routes['before']     = [];
        $this->routes['routes']     = [];
        $this->routes['parameters'] = [];
        $this->routes['after']      = [];
        $this->routes['end'] = function(){};
        
    }
    
    /* Metodo para teste */
    public function _setPath($path)
    {
        $this->path = $path;
    }
    
    public function start($callback)
    {
        $this->routes['start'] = $callback;
    }
    
    public function end($callback)
    {
        $this->routes['end'] = $callback;
    }
    
    public function get($path, $callback)
    {
        $conf = explode(':', $path, 2);
        
        $path = $conf[0];
        $parameter = $conf[1] ?? null;
        
        $this->routes['routes'][$path] = $callback;
        $this->routes['parameters'][$path] = $parameter;
        
        $this->actualPathConfi = $path;
        return $this;
    }
    
    public function before($callback)
    {
        $this->routes['before'][$this->actualPathConfi] = $callback;
        return $this;
    }
    
    public function after($callback)
    {
        $this->routes['after'][$this->actualPathConfi] = $callback;
        return $this;
    }
  
    public function getRouteWithParameter($path)
    {
        $routes = array_keys($this->routes['routes']);
        
        foreach ($routes as $route) {
            if (strpos($path, $route) !== false) {
                $parameter = str_replace([$route,'/'], '', $path);
                return $this->routes['routes'][$route]($parameter);
            }
        }
        
        return false;
    }
    
    public function run()
    {
        # Before
        if (array_key_exists($this->path, $this->routes['before'])) {
           $this->routes['before'][$this->path]();
        }
        
        # Route with parameter
        if (array_key_exists($this->path, $this->routes['routes'])) {
             $this->routes['routes'][$this->path]();
        } else {
            # Route less parameter
            $this->getRouteWithParameter($this->path);
        }

        # After
        if (array_key_exists($this->path, $this->routes['after'])) {
           $this->routes['after'][$this->path]();
        } 
        
    }
    
    public function __destruct() 
    {
        $this->routes['start']();
        $this->run();
        $this->routes['end']();
    }
 
}

