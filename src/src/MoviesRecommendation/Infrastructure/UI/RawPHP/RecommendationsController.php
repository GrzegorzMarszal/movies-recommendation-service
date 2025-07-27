<?php

declare(strict_types = 1);

namespace App\MoviesRecommendation\Infrastructure\UI\RawPHP;

use App\MoviesRecommendation\Application\Service\RecommendationService;
use App\MoviesRecommendation\Domain\Exception\CantFindRecommendationsException;
use App\MoviesRecommendation\Infrastructure\Resolver\StrategyFromKeyResolver;

readonly class RecommendationsController
{
    public function __construct(
        private RecommendationService $recommendationService,
        private StrategyFromKeyResolver $strategyResolver
    ) {
    }

    public function execute($strategyName): string
    {
        try {
            if ($strategyName !== null) {
                $this->recommendationService->setStrategy($this->strategyResolver->resolve($strategyName));
            }
        } catch (CantFindRecommendationsException $e) {
            //@TODO: Can add logging in a future

            //We return only general error to user
            return 'Nie można pobrać rekomendacji.' . PHP_EOL;
        }

        /**
         *  @TODO
         *  It a future it would be great to extract rendering to a separate renderer
         *  to have a better testability and more "S"
         */
        $result = $this->recommendationService->getStrategyName() . PHP_EOL;
        foreach ($this->recommendationService->getRecommendations() as $movie) {
            $result .= '-> ' . $movie->getTitle() . PHP_EOL;
        }

        return $result;
    }
}