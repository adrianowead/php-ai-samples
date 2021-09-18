<?php

namespace App\Console\Commands;

use App\Challenges\TravellingSalesman as ChallengesTravellingSalesman;
use App\Genetic\TravellingSalesman;
use Illuminate\Console\Command;

class TravellingSalesmanProblem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tsp:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executa o problema do caixeiro viajante';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): void
    {
        $challenge = new ChallengesTravellingSalesman;

        $challenge->setCity(
            name: "A",
            maxPass: 1
        )->setCity(
            name: "B",
            maxPass: 1
        )->setCity(
            name: "C",
            maxPass: 1
        );

        $challenge->addRouteCost(
            from: "A",
            to: "B",
            cost: 2.0,
        )->addRouteCost(
            from: "A",
            to: "C",
            cost: 4.0,
        )->addRouteCost(
            from: "B",
            to: "C",
            cost: 1.0,
        );

        $challenge->setStartCity("A")
            ->setTargetCity("C");

        dd(
            $challenge,
            [
                'cities' => $challenge->getAllCities(),
                'routes' => $challenge->getAllRoutes(),
                'ready' => $challenge->readyToRun(),
            ],
        );

        $genetic = new TravellingSalesman(
            challenge: $challenge,
        );

        $genetic->fillPopulation();

        dump([
            'live' => $genetic->getCountLivePopulation(),
            'generation' => $genetic->getCountCurrentGeneration(),
            'global population' => $genetic->getCountGlobalPopulation(),
            'best individual' => $genetic->getBestIndividual(),
        ]);
    }
}
