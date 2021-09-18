<?php

namespace App\Challenges;

use Illuminate\Support\Collection;

final class TravellingSalesman
{
    private Collection $cities;
    private Collection $routes;

    private string $startCity;
    private string $targetCity;

    public function __construct()
    {
        $this->cities = new Collection;
        $this->routes = new Collection;
    }

    /**
     * As rotas são sempre bi direcionais, ou seja, o custo de ida é o mesmo de volta
     */
    public function addRouteCost(string $from, string $to, float $cost, int $maxPassFrom = 1, int $maxPassTo = 1): TravellingSalesman
    {
        if($cost <= 0) {
            throw new \Exception("O custo da rota precisa ser maior do que zero.");
        }

        if($maxPassFrom < 1) {
            throw new \Exception("É necessário permitir a passagem pela cidade de origem, pelo menos uma vez.");
        }

        if($maxPassTo < 1) {
            throw new \Exception("É necessário permitir a passagem pela cidade de destino, pelo menos uma vez.");
        }

        $routeId = self::getRouteId(
            from: $from,
            to: $to
        );

        if($this->routes->has($routeId)) {
            throw new \Exception("A rota informada já existe {$from} <-> {$to}.");
        }

        $this->routes->put($routeId, [
            'from' => $from,
            'to' => $to,
            'cost' => $cost,
        ]);

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

    public static function getRouteId(string $from, string $to): string
    {
        $routeId = [$from, $to];
        sort($routeId);

        return sha1(implode('', $routeId));
    }

    public function setStartCity(string $start): TravellingSalesman
    {
        if(!$this->cities->contains($start)) {
            throw new \Exception("A cidade de origem informada, não existe.");
        }

        $this->startCity = $start;

        return $this;
    }

    public function setTargetCity(string $target): TravellingSalesman
    {
        if(!$this->cities->contains($target)) {
            throw new \Exception("A cidade alvo informada, não existe.");
        }

        $this->targetCity = $target;

        return $this;
    }

    /**
     * Valida se o desafio contém todos os itens necessários para executar
     */
    public function readyToRun(): bool
    {
        $status = isset($this->startCity);

        if(!$status) {
            throw new \Exception("É necessário informar a cidade de início (start city).");
        }

        $status = $status && isset($this->targetCity);

        if(!$status) {
            throw new \Exception("É necessário informar a cidade de final (tartget city).");
        }

        $status = $status && isset($this->cities) && $this->cities->count() > 2;

        if(!$status) {
            throw new \Exception("É obrigatório ter pelo menos três cidades cadastradas (via rota).");
        }

        $status = $status && isset($this->routes) && $this->routes->count() > 2;

        if(!$status) {
            throw new \Exception("É obrigatório ter pelo menos duas rotas cadastradas (via rota).");
        }

        return $status;
    }
}
