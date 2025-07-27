<?php

declare(strict_types = 1);

namespace Tests\Unit\MoviesRecommendation\Application\Strategy;

use App\MoviesRecommendation\Application\Strategy\RandomStrategy;
use App\MoviesRecommendation\Domain\Entity\Movie;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RandomStrategy::class)]
class RandomStrategyTest extends TestCase
{
    public function testGivenStrategyIsCreated_whenGetNameIsCalled_thenItShouldReturnRandomName(): void
    {
        $count = 3;
        $strategy = new RandomStrategy($count);
        $name = $strategy->getName();

        $this->assertSame(sprintf(RandomStrategy::NAME, $count), $name);
    }


    public function testGivenMoreMoviesThanCount_whenGetRecommendationsIsCalled_thenItShouldReturnCorrectNumberOfMovies(): void
    {
        $movies = [
            new Movie('Pulp Fiction'),
            new Movie('Incepcja'),
            new Movie('Skazani na Shawshank'),
            new Movie('Ojciec chrzestny'),
            new Movie('Django'),
        ];
        $strategy = new RandomStrategy(3);
        $recommendations = $strategy->getRecommendations($movies);

        $this->assertCount(3, $recommendations);
    }

    public function testGivenFewerMoviesThanCount_whenGetRecommendationsIsCalled_thenItShouldReturnAllMovies(): void
    {
        $movies = [new Movie('Pulp Fiction'), new Movie('Incepcja')];
        $strategy = new RandomStrategy(5);
        $recommendations = $strategy->getRecommendations($movies);

        $this->assertCount(2, $recommendations);
        $this->assertEquals($movies, $recommendations);
    }

    public function testGivenEqualNumberOfMoviesToCount_whenGetRecommendationsIsCalled_thenItShouldReturnAllMovies(): void
    {
        $movies = [new Movie('Pulp Fiction'), new Movie('Incepcja'), new Movie('Skazani na Shawshank')];
        $strategy = new RandomStrategy(3);
        $recommendations = $strategy->getRecommendations($movies);

        $this->assertCount(3, $recommendations);
        $this->assertEquals($movies, $recommendations);
    }

    public function testGivenEmptyMovieList_whenGetRecommendationsIsCalled_thenItShouldReturnEmptyArray(): void
    {
        $strategy = new RandomStrategy();
        $recommendations = $strategy->getRecommendations([]);

        $this->assertEmpty($recommendations);
    }

    public function testGivenZeroCountInConstructor_whenGetRecommendationsIsCalled_thenItShouldThrowException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new RandomStrategy(0);
    }

    public function testGivenMovieList_whenGetRecommendationsIsCalled_thenReturnedMoviesShouldBeASubsetOfOriginalList(): void
    {
        // Given
        $originalMovies = [
            new Movie('Pulp Fiction'),
            new Movie('Incepcja'),
            new Movie('Skazani na Shawshank'),
            new Movie('Ojciec chrzestny'),
            new Movie('Django'),
            new Movie('Matrix'),
            new Movie('Leon zawodowiec'),
        ];
        $strategy = new RandomStrategy(3);
        $recommendations = $strategy->getRecommendations($originalMovies);

        $this->assertCount(3, $recommendations, 'It should return the correct number of movies.');

        foreach ($recommendations as $recommendedMovie) {
            $this->assertContains(
                $recommendedMovie,
                $originalMovies,
                'Each recommended movie must be an element of the original movie list.'
            );
        }
    }
}