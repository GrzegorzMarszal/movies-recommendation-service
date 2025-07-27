<?php

declare(strict_types = 1);

namespace App\MoviesRecommendation\Application\Strategy;

use App\MoviesRecommendation\Domain\Entity\Movie;

class WEvenStrategy implements RecommendationStrategyInterface
{
    public const NAME = 'Wszystkie filmy na literę W, ale tylko jeśli mają parzystą liczbę znaków w tytule';

    public function getRecommendations(array $movies): array
    {
        return array_values(array_filter($movies, function (Movie $movie) {
            $title = $movie->getTitle();
            /**
             * The task stated that it should be an even number of characters, not letters. If it had been letters,
             * I would have taken into account, for example, that a space is not a letter.
             */
            return mb_substr($title, 0, 1) === 'W' && mb_strlen($title) % 2 === 0;
        }));
    }
    
    public function getName(): string
    {
        return self::NAME;
    }
}