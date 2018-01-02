<?php
require_once __DIR__ . '/../Router.php';
require_once __DIR__ . '/Corrida.php';

use NewDay\Route\Router;

$app = new Router;

$tests = [
    'start' =>
        function() use($app) {
            
            $app->_setPath('/atleta/joao/campeao');
            $app->get('/atleta/{nome}/{posicao}', function() {
            
                return ['joao', 'campeao'];
            });
          
            $esperado = ['joao', 'campeao'];
            
            $ocorrido = $app->executeRoute();
            
            var_dump($esperado == $ocorrido);
        
        },
    'dois' =>
        function() use($app) {
            
            $app->_setPath('/atleta/joao/campeao');
            $app->get('/atleta/{nome}/{posicao}', 'Corrida@informacoes' );
          
            $esperado = ['joao', 'campeao'];
            
            $ocorrido = $app->executeRoute();
            
            var_dump($esperado == $ocorrido);
        
        },
    'tres' =>
        function() use($app) {
            
            $app->_setPath('/atleta/joao/campeao');
            $app->get('/atleta/{nome}/{posicao}', 'Corrida@informacoes' )
                    ->before(function () {
                        return ['joao', 'campeao'];
                    })
                    ->after(function() {
                        return ['joao', 'campeao'];
                    });
          
            $esperado = ['joao', 'campeao'];
            
            $ocorrido = $app->executeBefore();
            
            var_dump($esperado == $ocorrido);
            
            $esperadoAfter = ['joao', 'campeao'];
            $ocorridoAfter = $app->executeAfter();
            
            var_dump($ocorridoAfter == $esperadoAfter);
        
        },
    'quatro' =>
        function() use($app) {
            
            $app->_setPath('/corrida/5/dezembro');
            
            $app->get('/corrida/{saoPaulo:[0-9]}/{dezembro}', 'Corrida@informacoes');
          
            $esperado = ['5', 'dezembro'];
            
            $ocorrido = $app->executeRoute();
            
            var_dump($esperado == $ocorrido);
        
        },
                
    'cinco' =>
        function() use($app) {
            
            $app->_setPath('/cinco/5');
            
            $app->get('/cinco/{dezembro:[0-9]}', function() {
            
                return ['saoPaulo', 'dezembro'];
            });
          
            $esperado = ['saoPaulo', 'dezembro'];
            
            $ocorrido = $app->executeRoute();
            
            var_dump($esperado == $ocorrido);
        
        },
];
        
foreach($tests as $teste) {
    $teste();
}
