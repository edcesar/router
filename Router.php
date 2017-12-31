<?php
namespace NewDay\Route;

class Router
{
    public $routes;
    public $path;
    
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
  
    public function executeWithParameter($tipo)
    {
        $routes = array_keys($this->routes[$tipo]);
        
        foreach ($routes as $route) {
            if (strpos($this->path, $route) !== false) {
                $parameter = str_replace([$route,'/'], '', $this->path);
                return $this->routes[$tipo][$route]($parameter);
            }
        }
        
        return false;
    }
    
    public function executeBefore()
    {
        if (array_key_exists($this->path, $this->routes['before'])) {
           return $this->routes['before'][$this->path]();
        }
        
        # Before with parameter
        return $this->executeWithParameter('before');
      
    }
    
    public function executeAfter()
    {
        if (array_key_exists($this->path, $this->routes['after'])) {
           return $this->routes['after'][$this->path]();
        }
        
        # After with parameter
        return $this->executeWithParameter('after');
    }
    
    public function executeRoute()
    {
        # Route less parameter
        if (array_key_exists($this->path, $this->routes['routes'])) {
             return $this->routes['routes'][$this->path]();
        } 
            
        # Route with parameter
        return $this->executeWithParameter('routes');
    }
    
    public function run()
    {
        $this->executeBefore();
        
        $this->executeRoute();

        $this->executeAfter();
        
    }
    
    public function __destruct() 
    {
        $this->routes['start']();
        $this->run();
        $this->routes['end']();
    }
 
}

