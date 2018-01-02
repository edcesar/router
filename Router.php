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
        $rota = preg_replace("/{([a-z A-Z 0-9]+):/", '{', $path);
        
        $rota = preg_replace("/{\[(...)\]}/", '([$1])', $rota);

        $rota = preg_replace("/{(.*?)}/", '(.*)', $rota);

        $rota = str_replace(['{','}', '/'], ['(',')', '\/'], $rota);
        
        $this->routes['routes'][$rota] = $callback;
        
        $this->actualPathConfi = $rota;
        
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
  
    public function executeBefore()
    {
        return $this->execute('before'); 
    }
    
    public function executeAfter()
    {
        return $this->execute('after'); 
    }
     
    public function executeRoute()
    {
       return $this->execute('routes');
    }
    
    public function execute($execute)
    {
        
        foreach ($this->routes[$execute] as $route => $execute) {
           
            if (preg_match("/{$route}/", $this->path, $match)) {
  
                if (count($match) > 1) {
                    array_shift($match);
                    $parameter = $match;
                } else {
                    $parameter = [];
                }
                
                if (is_callable($execute)) {
                    return $execute(...$parameter);
                }
                return $this->executeClass($execute, $parameter);
            }
        }
      
    }
    
    public function executeClass($class, $parameter = [])
    {
        list($class, $method) = explode('@', $class);
        
        $obj = new $class;
        return $obj->$method(...$parameter);
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

