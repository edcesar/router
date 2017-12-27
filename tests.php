<?php
require_once __DIR__ . '/Router.php';
use NewDay\Route\Router;

$app = new Router;

$tests = [
    'testRetornoCallbackString' => 
        function () use($app) {
            $esperado = 'Hello executado!';
            
            $app->_setPath('/hello');
                
            $app->get('/hello', function() {
                return 'Hello executado!';
            });

            $ocorrido = $app->run('/hello');

            var_dump($esperado == $ocorrido);
        },
                
    'testCadastraDuasRotasEdeveExecutarAsDuas' =>            
        function() use($app) {
            
            $esperado = 'clientes listados!';

            $app->_setPath('/clientes');
             
            $app->get('/clientes', function() {
                return 'clientes listados!';
            });

            $ocorrido = $app->run('/clientes');
            var_dump($esperado == $ocorrido);
        },
               
    'testDeveCadastrarERetornarOParametro' =>
       function() use($app) {
            
            $app->_setPath('/cliente/joao@gmail.com');
            
            $app->get('/cliente/:email', function ($email) {
                return $email;
            });
            
            $esperado = 'joao@gmail.com';
            $ocorrido = $app->run('/cliente/joao@gmail.com');
            
            var_dump($esperado == $ocorrido);
       }, 
               
     'testDeveExecutarTarefaAntesDaRotaSerExecutada'=>
        function() use ($app) {
            $app->_setPath('/cliente/joao@gmail.com');
            
            $app->get('/cliente/:email', function ($email) {
                return $email;
            })->before(function() {
                return 'executado antes!';
            });
            
            $esperado = 'executado antes!';
            $ocorrido = $app->run();
            
            var_dump($esperado == $ocorrido);
        },
    
    'testDeveExecutarTarefaDepoisDaRotaSerExecutada'=>
        function() use ($app) {
            $app->_setPath('/cliente/joao@gmail.com');
            
            $app->get('/cliente/:email', function ($email) {
                return $email;
            })->before(function() {
                return 'executado depois!';
            });
            
            $esperado = 'executado depois!';
            $ocorrido = $app->run();
            
            var_dump($esperado == $ocorrido);
        }                 
        
];
        
        
        
        
        
        
        
foreach($tests as $teste) {
    $teste();
}
