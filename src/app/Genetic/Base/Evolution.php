<?php

namespace App\Genetic\Base;

use Illuminate\Support\Collection;

interface Evolution{
    /**
     * Adiciona o novo indivíduo na população
     */
    public function born(Individual $individual): void;

    /**
     * Completa a população com base nos indivíduos existentes, se não existirem, cria indivíduos novos
     * Cruzando os gênis deles e executando mutações aleatórias
     */
    public function fillPopulation(): void;

    /**
     * Executa o cruzamento "genético" (atributos) dos indivíduos informados
     */
    public function crossover(Individual $a, Individual $b): Individual;

    /**
     * Seleção natual, manter apenas X elementos mais ápitos
     */
    public function selection(int $nBest = 2): Collection;

    /**
     * Executa uma mutação aleatória nos indivíduos
     */
    public function mutate(Individual &$individual): void;

    /**
     * Cálculo de aptidão do indivíduo
     */
    public function fitness(): float;
}