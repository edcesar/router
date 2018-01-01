<?php

class Corrida
{
    public function start()
    {
        return 'Corrida iniciada!';
    }
    
    public function campeao($campeao)
    {
        return $campeao;
    }
    
    public function informacoes($nome, $posicao)
    {
        return [$nome, $posicao];
    }
}