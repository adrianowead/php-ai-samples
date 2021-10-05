<?php

namespace App\Genetic\Base;

final class Individual{
    // atributo fixo e inerente aos indivíduos
    private float $fitness = 0.0;

    public function __construct(
        private array $dna,
    ) {}

    public function __get($name): mixed
    {
        return match ($name) {
            "fitness" => $this->fitness,
            default => throw new \Exception("property {$name} not found!")
        };
    }

    /**
     * Definir os atributos de forma dinâmica
     * A regra é sempre ser um float, para generalizar
     */
    public function __set(string $prop, float $value): void
    {
        $this->$prop = $value;
    }

    /**
     * Recebe mutração no DNA
     */
    public function mutate(array $dna): void
    {
        if(empty($dna)) {
            throw new \Exception("A fita de DNS não pode ser vazia");
        }

        $this->dna = $dna;
    }

    public function getDna(): array
    {
        return $this->dna;
    }
}
