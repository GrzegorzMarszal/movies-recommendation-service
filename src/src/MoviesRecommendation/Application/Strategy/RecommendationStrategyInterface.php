<?php

declare(strict_types = 1);

namespace App\MoviesRecommendation\Application\Strategy;

use App\MoviesRecommendation\Domain\Entity\Movie;

interface RecommendationStrategyInterface
{
    /**
     * @param Movie[] $movies
     * @return Movie[]
     */
    public function getRecommendations(array $movies): array;

    public function getName(): string;
}