<?php

declare(strict_types = 1);

namespace App\MoviesRecommendation\Application\Strategy;

use App\MoviesRecommendation\Domain\Entity\Movie;

class MultiWordStrategy implements RecommendationStrategyInterface
{
    public const NAME = 'Wszystkie tytuły, które składają się z więcej niż 1 słowa';

    public function getRecommendations(array $movies): array
    {
        return array_values(array_filter($movies, function (Movie $m) {
            // Split the title into parts, using anything that is not a unicode letter or number as a separator
            $potentialWords = preg_split(
                '/[^\p{L}\p{N}]+/u', // Separator not a letter or number
                $m->getTitle(),
                -1,
                PREG_SPLIT_NO_EMPTY
            );

            $actualWords = array_filter(
                $potentialWords,
                fn($word) => mb_strlen($word, 'UTF-8') > 1
            );

            return count($actualWords) > 1;
        }));
    }

    public function getName(): string
    {
        return self::NAME;
    }
}