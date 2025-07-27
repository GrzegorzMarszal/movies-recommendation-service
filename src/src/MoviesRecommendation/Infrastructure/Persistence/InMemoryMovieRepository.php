<?php

declare(strict_types = 1);

namespace App\MoviesRecommendation\Infrastructure\Persistence;

use App\MoviesRecommendation\Application\Port\MovieRepositoryInterface;
use App\MoviesRecommendation\Domain\Entity\Movie;

class InMemoryMovieRepository implements MovieRepositoryInterface
{
    /**
     * @var string[]
     */
    private array $movies = [];

    /**
     * @param string $moviesFilePath
     */
    public function __construct(string $moviesFilePath)
    {
        if (!file_exists($moviesFilePath)) {
            throw new \InvalidArgumentException(sprintf(
                'File "%s" not found',
                $moviesFilePath
            ));
        }

        require $moviesFilePath;

        /** @var  string[] $movies */
        $this->convertMoviesArrayToEntity($movies);
    }

    public function findAll(): array
    {
        return $this->movies;
    }

    private function convertMoviesArrayToEntity(array $movies): void
    {
        $this->movies = array_map(
            fn(string $title) => new Movie(trim($title)),
            array_filter(
                $movies,
                fn($item) => is_string($item)
            )
        );
    }
}