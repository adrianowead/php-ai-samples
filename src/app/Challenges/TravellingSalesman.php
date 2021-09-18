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
    public function addRouteCost(string $from, string $to, float $cost): TravellingSalesman
    {
        if($cost <= 0) {
            throw new \Exception("O custo da rota precisa ser maior do que zero.");
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

        if(!$this->cityAlreadyExists($from)) $this->addCity(name: $from);
        if(!$this->cityAlreadyExists($to)) $this->addCity(name: $to);

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
        if(!$this->cityAlreadyExists($start)) {
            throw new \Exception("A cidade de origem informada, não existe.");
        }

        $this->startCity = $start;

        return $this;
    }

    public function setTargetCity(string $target): TravellingSalesman
    {
        if(!$this->cityAlreadyExists($target)) {
            throw new \Exception("A cidade alvo informada, não existe.");
        }

        $this->targetCity = $target;

        return $this;
    }

    /**
     * Criar cidade
     */
    public function addCity(string $name): TravellingSalesman
    {
        if($this->cityAlreadyExists($name)) {
            throw new \Exception("A cidade informada já existe.");
        }

        $this->cities->put($name, [
            'maxPass' => 0,
            'countPassed' => 0,
        ]);

        return $this;
    }

    /**
     * Alterar parâmetros
     */
    public function setCity(string $name, int $maxPass = null, int $countPassed = null): TravellingSalesman
    {
        if(!$this->cityAlreadyExists($name)) {
            $this->addCity(
                name: $name,
            );
        }

        $params = $this->cities->get($name);

        if($maxPass != null) $params['maxPass'] = $maxPass;
        if($countPassed != null) $params['countPassed'] = $countPassed;

        if(!empty($params)) {
            $this->cities = $this->cities->replace([$name => $params]);
        }

        return $this;
    }

    public function cityAlreadyExists(string $name): bool
    {
        return $this->cities->has($name);
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

        $status = $status && $this->cities->where('maxPass', 0)->count() == 0;

        if(!$status) {
            throw new \Exception("Todas as cidades precisam permitir pelo menos uma passagem (maxPass).");
        }

        $status = $status && $this->cities->where('countPassed', 0)->count() == $this->cities->count();

        if(!$status) {
            throw new \Exception("Todas as cidades precisam começar com o contador de passagem zerado (countPassed).");
        }

        $status = $status && isset($this->routes) && $this->routes->count() > 2;

        if(!$status) {
            throw new \Exception("É obrigatório ter pelo menos duas rotas cadastradas (via rota).");
        }

        return $status;
    }
}