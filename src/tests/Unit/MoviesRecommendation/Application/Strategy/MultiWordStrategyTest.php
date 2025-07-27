<?php

declare(strict_types=1);

namespace Tests\Unit\MoviesRecommendation\Application\Strategy;

use App\MoviesRecommendation\Application\Strategy\MultiWordStrategy;
use App\MoviesRecommendation\Domain\Entity\Movie;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(MultiWordStrategy::class)]
class MultiWordStrategyTest extends TestCase
{
    private MultiWordStrategy $strategy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->strategy = new MultiWordStrategy();
    }

    public function testGivenStrategyIsCreated_whenGetNameIsCalled_thenItShouldReturnMultiWordName(): void
    {
        $name = $this->strategy->getName();

        $this->assertSame(MultiWordStrategy::NAME, $name);
    }

    /**
     * @param string[] $titlesToTest
     * @param string[] $expectedTitles
     */
    #[DataProvider('movieTitlesProvider')]
    public function testGivenListOfMovies_whenGetRecommendationsIsCalled_thenItShouldReturnOnlyValidMultiWordTitles(
        array $titlesToTest,
        array $expectedTitles
    ): void {
        $movies = array_map(fn(string $title) => new Movie($title), $titlesToTest);

        $recommendations = $this->strategy->getRecommendations($movies);

        $recommendedTitles = array_map(fn(Movie $movie) => $movie->getTitle(), $recommendations);

        $this->assertCount(count($expectedTitles), $recommendedTitles);
        $this->assertEquals($expectedTitles, $recommendedTitles);
    }

    public static function movieTitlesProvider(): array
    {
        return [
            'Given a standard multi-word title, it should be returned' => [
                'titlesToTest' => ['Dzień Świra'],
                'expectedTitles' => ['Dzień Świra'],
            ],
            'Given a single-word title, it should be ignored' => [
                'titlesToTest' => ['Miś'],
                'expectedTitles' => [],
            ],
            'Given a title with a single-letter word, it should be ignored' => [
                'titlesToTest' => ['Drużyna A'],
                'expectedTitles' => [],
            ],
            'Given a title with multiple single-letter words, it should be returned if other words exist' => [
                'titlesToTest' => ['I.T. Niezły wariat'],
                'expectedTitles' => ['I.T. Niezły wariat'],
            ],
            'Given a title with numbers and multiple words, it should be returned' => [
                'titlesToTest' => ['Siedem 7 razy'],
                'expectedTitles' => ['Siedem 7 razy'],
            ],
            'Given a mix of valid and invalid titles, it should filter correctly' => [
                'titlesToTest' => ['Pulp Fiction', 'Rocky', 'Skazani na Shawshank', 'W'],
                'expectedTitles' => ['Pulp Fiction', 'Skazani na Shawshank'],
            ],
            'Given an empty movie list, it should return an empty list' => [
                'titlesToTest' => [],
                'expectedTitles' => [],
            ],
            'Given a list with no multi-word titles, it should return an empty list' => [
                'titlesToTest' => ['Rocky', 'Gladiator'],
                'expectedTitles' => [],
            ]
        ];
    }
}