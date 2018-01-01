<?php
require_once __DIR__ . '/../Router.php';
require_once __DIR__ . '/Corrida.php';

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

            $ocorrido = $app->executeRoute();

            var_dump($esperado == $ocorrido);
        },
                
    'testCadastraDuasRotasEdeveExecutarAsDuas' =>            
        function() use($app) {
            
            $esperado = 'clientes listados!';

            $app->_setPath('/clientes');
             
            $app->get('/clientes', function() {
                return 'clientes listados!';
            });

            $ocorrido = $app->executeRoute();
            var_dump($esperado == $ocorrido);
        },
               
    'testDeveCadastrarERetornarOParametro' =>
       function() use($app) {
            
            $app->_setPath('/cliente/joao');
            
            $app->get('/cliente/:email', function ($email) {
                return $email;
            });
            
            $esperado = 'joao';
            $ocorrido = $app->executeRoute();
            
            var_dump($esperado == $ocorrido);
       }, 
               
     'testDeveExecutarTarefaAntesDaRotaSerExecutada'=>
        function() use ($app) {
            $app->_setPath('/alunos/joao@gmail.com');
            
            $app->get('/alunos/:email', function ($email) {
                return $email;
            })->before(function() {
                return 'executado antes!';
            });
           
            $esperado = 'executado antes!';
            $ocorrido = $app->executeBefore();
         
            var_dump($esperado == $ocorrido);
        },
    
    'testDeveExecutarTarefaDepoisDaRotaSerExecutada'=>
        function() use ($app) {
            $app->_setPath('/funcionario/joao@gmail.com');
            
            $app->get('/funcionario/:email', function ($email) {
                return $email;
            })->after(function() {
                return 'executado depois!';
            });
            
            $esperado = 'executado depois!';
            $ocorrido = $app->executeAfter();
            
            var_dump($esperado == $ocorrido);
        },
    
    'testDeveExecutarClassEMetodo' =>
        function() use ($app) {
            $app->_setPath('/corrida');
            $app->get('/corrida', 'Corrida@start');
            
            $esperado = 'Corrida iniciada!';
            $ocorrido = $app->executeRoute();
            
            var_dump($esperado == $ocorrido);
        },
        'testDeveExecutarClassEMetodoComParametro' =>
        function() use($app) {
            $app->_setPath('/atleta/campeao/joaoCorredor');
            $app->get('/atleta/campeao/:campeao', 'Corrida@campeao');
            
            $esperado = 'joaoCorredor';
            $ocorrido = $app->executeRoute();
            
            var_dump($esperado == $ocorrido);
        }

];
        

        
foreach($tests as $teste) {
    $teste();
}
