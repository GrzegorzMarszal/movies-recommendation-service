<?php

declare(strict_types = 1);

namespace App\MoviesRecommendation\Application\Strategy;

readonly class RandomStrategy implements RecommendationStrategyInterface
{
    public const NAME = '%s losowe tytuły';

    public function __construct(private int $count = 3) {
        if ($this->count < 1) {
            throw new \InvalidArgumentException('Count must be greater than 0');
        }
    }

    public function getRecommendations(array $movies): array
    {
        if (count($movies) <= $this->count) {
            return $movies;
        }

        $randomKeys = (array) array_rand($movies, $this->count);
        return array_map(fn($key) => $movies[$key], $randomKeys);
    }

    public function getName(): string
    {
        return sprintf(self::NAME, $this->count);
    }
}