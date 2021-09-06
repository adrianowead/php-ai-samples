<?php

namespace App\Console\Commands;

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
        $genetic = new TravellingSalesman;
        $genetic->fillPopulation();

        dump([
            'live' => $genetic->getCountLivePopulation(),
            'generation' => $genetic->getCountCurrentGeneration(),
            'global population' => $genetic->getCountGlobalPopulation(),
            'best individual' => $genetic->getBestIndividual(),
        ]);
    }
}