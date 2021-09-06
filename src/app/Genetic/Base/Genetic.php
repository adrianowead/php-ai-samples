<?php

namespace App\Genetic\Base;

use Illuminate\Support\Collection;

abstract class Genetic implements Evolution{
    protected Collection $population; // coleção com todos os indivíduos vivos
    protected int $generation = 1; // contador de gerações
    protected int $globalCountPopulation = 0; // contador de total de indivíduos entre todas as gerações

    public function __construct(
        protected int $limitPopulation = 100, // limite populacional a cada geração
    ) {
        $this->population = new Collection;
    }

    /**
     * Adiciona o novo indivíduo na população
     */
    public function born(Individual $individual): void
    {
        $this->population->add($individual);
        $this->globalCountPopulation++;
    }

    /**
     * Seleção natual, manter apenas X elementos mais ápitos
     */
    public function selection(int $nBest = 2): Collection
    {
        $this->orderPopulationByFitness();

        $this->population = $this->population->slice(0, $nBest);

        return $this->population;
    }

    public function getCountGlobalPopulation(): int
    {
        return $this->globalCountPopulation ?? 0;
    }

    public function getCountLivePopulation(): int
    {
        return $this->population->count();
    }

    public function getCountCurrentGeneration(): int
    {
        return $this->generation;
    }

    public function getBestIndividual(): Individual
    {
        $this->orderPopulationByFitness();

        return $this->population->first();
    }

    private function orderPopulationByFitness(): void
    {
        $this->population = $this->population->sortByDesc(fn(Individual $individual) => $individual->fitness);
    }
}