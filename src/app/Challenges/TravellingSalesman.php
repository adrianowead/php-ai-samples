<?php

namespace App\Challenges;

use Illuminate\Support\Collection;

final class TravellingSalesman
{
    private Collection $cities;
    private Collection $routes;

    public function __construct()
    {
        $this->cities = new Collection;
        $this->routes = new Collection;
    }

    /**
     * As rotas são sempre bi direcionais, ou seja, o custo de ida é o mesmo de volta
     */
    public function addRouteCost(string $from, string $to, float $cost): TravellingSalesman
    {
        if($cost <= 0) {
            throw new \Exception("O custo da rota precisa ser maior do que zero.");
        }

        $routeId = [$from, $to];
        sort($routeId);

        $routeId = sha1(implode('', $routeId));

        if($this->routes->has($routeId)) {
            throw new \Exception("A rota informada já existe {$from} <-> {$to}.");
        }

        $route = [
            'from' => $from,
            'to' => $to,
            'cost' => $cost,
        ];

        $this->routes->put($routeId, $route);

        if(!$this->cities->contains($from)) $this->cities->add($from);
        if(!$this->cities->contains($to)) $this->cities->add($to);

        return $this;
    }

    public function getAllCities(): array
    {
        return $this->cities->sort()->all();
    }

    public function getAllRoutes(): array
    {
        return $this->routes->all();
    }
}