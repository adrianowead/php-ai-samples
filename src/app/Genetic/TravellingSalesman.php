<?php

namespace App\Genetic;

use App\Challenges\TravellingSalesman as ChallengesTravellingSalesman;
use App\Genetic\Base\Genetic;
use App\Genetic\Base\Individual;
use Swoole\Coroutine\WaitGroup;

final class TravellingSalesman extends Genetic
{
    private $requiredChromosomes = [];

    private $mutateRules = [
        'probability' => 0.1, // 0.1 > 1.0
    ];

    public function __construct(
        private ChallengesTravellingSalesman $challenge,
        protected int $limitPopulation = 100,
    ) {
        parent::__construct(limitPopulation: $limitPopulation);

        $this->validateChallenge();

        $this->extractChromosomesFromCallenge();
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

        $this->fillEmptyPopulation(
            limitToFill: $limitToFill,
            waitExec: true,
        );

        $this->avoidTwins();

        if($this->population->count() > 0) {
            //
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
                        $individual = new Individual(dna: $ctx->requiredChromosomes);

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
    private function doMutate(Individual $individual): void
    {
        $probability = mt_rand(1, 10) <= ( $this->mutateRules['probability'] * 10 );

        $probability = 1;

        if($probability) {
            $to = $from = mt_rand(0, sizeof($individual->getDna()) - 1);

            while($to == $from) {
                $to = mt_rand(0, sizeof($individual->getDna()) - 1);
            }

            $dnaMutation = $individual->getDna();

            $swap = $dnaMutation[$to];
            $dnaMutation[$to] = $dnaMutation[$from];
            $dnaMutation[$from] = $swap;

            $individual->mutate($dnaMutation);

            $individual->fitness = $this->fitness(individual: $individual);
        }
    }

    private function randFloat(float $min, float $max): float
    {
        $range = $max - $min;
        $num = $min + $range * (mt_rand() / mt_getrandmax());

        return $num;
    }

    /**
     * A diversidade genética é o que garante a evolução
     * Por tanto, não permitir que gêmeos idênticos geneticamente
     * permaneçam na população
     */
    private function avoidTwins(): void
    {
        $this->population = $this->population->unique(function(Individual $individual){
            return $individual->getDna();
        });
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
     *
     * No caso do caixeiro viajante o objetivo é:
     *
     * - Visitar todas as cidades
     * - Limitado à quantidade de visitas permitidas em cada cidade
     * - Com o menor custo possível, soma de todas as rotas visitas e todas as vezes visitadas
     * - Iniciar na cidade 'start' e terminar na cidade 'target'
     */
    public function fitness(Individual $individual): float
    {
        dd("Calcular corretamente o fitness do indivíduo, para então criar a função que verifica se algum deles resolve o problema, o fitness vai de 0.0 até 1.0, sendo 1.0 a solução");

        return 0.0;
    }

    /**
     * Com base nas propriedades do desafio
     * Montar a estrutura base dos cromosomos que irão compor o DNA dos indivíduos
     */
    private function extractChromosomesFromCallenge(): void
    {
        $cities = $this->challenge->getAllCities();

        // multiplicando as cidades e possibilidades de visitas

        // Exemplo: cidade A com 2 visitas
        // DNA: AA

        // Exemplo: cidade A com 2 visitas, B com 4 visitas e C com 3 visitas
        // DNA: AABBBBCC

        // com isso teremos a quantidade de casas, as mutações serão a mudança de uma casa em outra.
        // mantendo a proporção da quantidade de cada um.

        // Exemplo: DNA base AABBBBCC
        // Mutação: ABABCBCB

        $dna = [];

        foreach($cities as $id => $city) {
            $dna = array_merge($dna, array_fill(0, $city['maxPass'], $id));
        }

        $this->requiredChromosomes = $dna;
    }
}
