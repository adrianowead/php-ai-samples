<?php

namespace App\Genetic;

use App\Challenges\TravellingSalesman as ChallengesTravellingSalesman;
use App\Genetic\Base\Genetic;
use App\Genetic\Base\Individual;
use Swoole\Coroutine\WaitGroup;

final class TravellingSalesman extends Genetic{
    private $requiredChromosomes = [
        'input' => [
            ''
        ],
        'layer0' => [],
        'output' => [],
    ];

    private $mutateRules = [
        'probability' => 0.1, // 0.1 > 1.0
    ];

    public function __construct(
        private ChallengesTravellingSalesman $challenge,
        protected int $limitPopulation = 100,
    ) {
        parent::__construct(limitPopulation: $limitPopulation);

        $this->validateChallenge();
    }

    public function validateChallenge(): TravellingSalesman
    {
        if(!$this->challenge->readyToRun()) {
            throw new \Exception("O desafio não atende a todos os requisitos para execução.");
        }

        return $this;
    }

    /**
     * Completa a população com base nos indivíduos existentes, se não existirem, cria indivíduos novos
     * Cruzando os gênis deles e executando mutações aleatórias
     */
    public function fillPopulation(): void
    {
        if($this->population->count() >= $this->limitPopulation) return ;

        $limitToFill = $this->limitPopulation - $this->population->count();

        $doEvolution = $this->population->count() > 0;

        $this->fillEmptyPopulation(
            limitToFill: $limitToFill,
            waitExec: true,
        );

        if($doEvolution) {

        }
    }

    /**
     * Completa a população até o limite informado
     */
    private function fillEmptyPopulation(int $limitToFill, bool $waitExec = false): void
    {
        $list = array_chunk(array_fill(0, $limitToFill, null), 10);

        $ctx = $this;

        // preenchimento da população em lotes
        // processmento paralelo via coroutine
        \Co\run(function() use ($ctx, $list, $waitExec) {
            $wg = false;

            if($waitExec) $wg = new WaitGroup();

            foreach($list as $chunk) {
                go(function() use ($ctx, $chunk, $wg, $waitExec) {
                    if($waitExec) $wg->add();

                    $count = sizeof($chunk);

                    for($y = 0; $y < $count; $y++) {
                        $individual = new Individual;

                        foreach( $ctx->requiredChromosomes as $chromosome => $value ){
                            $individual->$chromosome = $value;
                        }

                        $ctx->mutate($individual);

                        $ctx->born($individual);
                    }

                    if($waitExec) $wg->done();
                });
            }

            if($waitExec) $wg->wait(1);
        });
    }

    /**
     * Executa uma mutação nos indivíduos
     * Neste caso seguindo especificações do problema do caixeiro viajante
     */
    public function mutate(Individual &$individual): void
    {
        $this->doMutate($individual);
    }

    /**
     * Executa uma mutação aleatória no atributo relacionao a este problema
     */
    private function doMutate(Individual &$individual): void
    {
        $probability = mt_rand(1, 10) <= ( $this->mutateRules['probability'] * 10 );

        if($probability) {
            $list = array_keys($this->mutateRules['chromosomes']);
            shuffle($list);

            $chromosome = $list[0];

            $individual->$chromosome = $this->randFloat(
                $this->mutateRules['chromosomes'][$chromosome]['min'],
                $this->mutateRules['chromosomes'][$chromosome]['max'],
            );

            $individual->fitness = $this->randFloat(0.2,0.9);
        }
    }

    private function randFloat(float $min, float $max): float
    {
        $range = $max - $min;
        $num = $min + $range * (mt_rand() / mt_getrandmax());

        return $num;
    }

    /**
     * Executa o cruzamento "genético" (atributos) dos indivíduos informados
     * Neste caso seguindo especificações do problema do caixeiro viajante
     */
    public function crossover(Individual $a, Individual $b): Individual
    {
        return new Individual;
    }

    /**
     * Cálculo de aptidão do indivíduo
     */
    public function fitness(): float
    {
        return 0.0;
    }
}