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
  
    public function executeWithParameter($tipo)
    {
        $routes = array_keys($this->routes[$tipo]);
        
        foreach ($routes as $route) {
            if (strpos($this->path, $route) !== false) {
                $parameter = str_replace($route, '', $this->path);
                $parameter = explode('/', $parameter);
                $execute = $this->routes[$tipo][$route];
                
                if (is_callable($execute)) {
                    return $this->routes[$tipo][$route](...$parameter);
                }
                
                return $this->executeClass($execute, $parameter);
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
    
    public function executeClass($class, $parameter = [])
    {
        list($class, $method) = explode('@', $class);
        
        $obj = new $class;
        return $obj->$method(...$parameter);
    }
    
    public function executeRoute()
    {

        # Route less parameter
        if (array_key_exists($this->path, $this->routes['routes'])) {
            
            $execute = $this->routes['routes'][$this->path];
            if (is_callable($execute)) {
                return $execute();
            }
            return $this->executeClass($execute);
        } 
            
        # Route with parameter
        return $this->executeWithParameter('routes');
    }
    
    public function run()
    {
        $this->routes['start']();
        $this->executeBefore();
        $this->executeRoute();
        $this->executeAfter();
        $this->routes['end']();
    }
    
    public function __destruct() 
    {
        $this->run();   
    }
 
}

