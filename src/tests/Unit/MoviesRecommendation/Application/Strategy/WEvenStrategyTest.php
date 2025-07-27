<?php

declare(strict_types = 1);

namespace Tests\Unit\MoviesRecommendation\Application\Strategy;

use App\MoviesRecommendation\Application\Strategy\WEvenStrategy;
use App\MoviesRecommendation\Domain\Entity\Movie;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(WEvenStrategy::class)]
class WEvenStrategyTest extends TestCase
{
    private WEvenStrategy $strategy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->strategy = new WEvenStrategy();
    }

    /**
     * @test
     */
    public function testGivenStrategyIsCreated_whenGetNameIsCalled_thenItShouldReturnWEvenName(): void
    {
        $name = $this->strategy->getName();

        $this->assertSame(WEvenStrategy::NAME, $name);
    }

    #[DataProvider('movieTitlesProvider')]
    public function testGivenListOfMovies_whenGetRecommendationsIsCalled_thenItShouldReturnOnlyTitlesStartingWithWAndWithEvenLength(
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
            'given a title starting with W and even length, it should be returned' => [
                'titlesToTest' => ['Władca Pierścienia'],
                'expectedTitles' => ['Władca Pierścienia'],
            ],
            'given a title starting with W but odd length, it should be ignored' => [
                'titlesToTest' => ['Wielki Gatsby'],
                'expectedTitles' => [],
            ],
            'given a title with even length but not starting with W, it should be ignored' => [
                'titlesToTest' => ['Pulp Fiction'],
                'expectedTitles' => [],
            ],
            'given a title starting with lowercase w, it should be ignored (case-sensitive)' => [
                'titlesToTest' => ['władca pierścienia'],
                'expectedTitles' => [],
            ],
            'given a mix of valid and invalid titles, it should filter correctly' => [
                'titlesToTest' => ['Władca Pierścienia', 'Wielki Gatsby', 'Pulp Fiction', 'Whiplash'],
                'expectedTitles' => ['Władca Pierścienia', 'Whiplash'],
            ],
            'given an empty movie list, it should return an empty list' => [
                'titlesToTest' => [],
                'expectedTitles' => [],
            ],
            'given a list with no matching titles, it should return an empty list' => [
                'titlesToTest' => ['Rocky', 'Gladiator', 'W', 'Wielka ucieczka'],
                'expectedTitles' => [],
            ],
        ];
    }
}