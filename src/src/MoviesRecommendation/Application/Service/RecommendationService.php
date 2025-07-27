<?php

declare(strict_types = 1);

namespace App\MoviesRecommendation\Application\Service;

use App\MoviesRecommendation\Application\Port\MovieRepositoryInterface;
use App\MoviesRecommendation\Application\Strategy\RecommendationStrategyInterface;
use App\MoviesRecommendation\Domain\Entity\Movie;

class RecommendationService
{
    private RecommendationStrategyInterface $strategy;

    public function __construct(
        RecommendationStrategyInterface $defaultStrategy,
        private readonly MovieRepositoryInterface $movieRepository
    ) {
        $this->strategy = $defaultStrategy;
    }

    public function setStrategy(RecommendationStrategyInterface $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function getStrategyName(): string
    {
        return $this->strategy->getName();
    }

    /**
     * @return Movie[]
     */
    public function getRecommendations(): array
    {
        $allMovies = $this->movieRepository->findAll();
        $this->removeDuplicates($allMovies);

        return $this->strategy->getRecommendations(
           $allMovies
        );
    }

    private function removeDuplicates(array &$movies): void
    {
        $addedTitles = [];
        /** @var Movie $movie */
        foreach ($movies as $key => $movie) {
            $title = $movie->getTitle();
            if (isset($addedTitles[$title])) {
               unset($movies[$key]);
            } else {
                $addedTitles[$title] = true;
            }
        }
    }
}