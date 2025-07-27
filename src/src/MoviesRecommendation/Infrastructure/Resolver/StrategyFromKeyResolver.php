<?php

declare(strict_types = 1);

namespace App\MoviesRecommendation\Infrastructure\Resolver;

use App\MoviesRecommendation\Application\Strategy\RecommendationStrategyInterface;
use App\MoviesRecommendation\Domain\Exception\CantFindRecommendationsException;

class StrategyFromKeyResolver
{
    /**
     * @param RecommendationStrategyInterface[] $availableStrategies
     */
    public function __construct(
        private readonly array $availableStrategies = []
    ) {
    }

    /**
     * @throws CantFindRecommendationsException
     */
    public function resolve(string $key): RecommendationStrategyInterface
    {
        return $this->availableStrategies[$key] ?? throw new CantFindRecommendationsException(
            sprintf('Strategy "%s" not found', $key)
        );
    }
}