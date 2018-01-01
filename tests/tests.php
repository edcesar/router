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
        
        }            
];
        
foreach($tests as $teste) {
    $teste();
}
