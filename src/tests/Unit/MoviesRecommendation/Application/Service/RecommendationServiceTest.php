<?php

declare(strict_types=1);

namespace Tests\Unit\MoviesRecommendation\Application\Service;

use App\MoviesRecommendation\Application\Port\MovieRepositoryInterface;
use App\MoviesRecommendation\Application\Service\RecommendationService;
use App\MoviesRecommendation\Application\Strategy\RandomStrategy;
use App\MoviesRecommendation\Application\Strategy\RecommendationStrategyInterface;
use App\MoviesRecommendation\Domain\Entity\Movie;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(RecommendationService::class)]
class RecommendationServiceTest extends TestCase
{
    private MovieRepositoryInterface&MockObject $movieRepositoryMock;
    private RecommendationStrategyInterface&MockObject $strategyMock;
    private RecommendationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->movieRepositoryMock = $this->createMock(MovieRepositoryInterface::class);
        $this->strategyMock = $this->createMock(RecommendationStrategyInterface::class);

        $this->service = new RecommendationService($this->strategyMock, $this->movieRepositoryMock);
    }

    public function testGivenDependencies_whenGetRecommendationsIsCalled_thenItShouldUseRepositoryAndStrategyCorrectly(): void
    {
        $movies = [new Movie('Test Movie 1'), new Movie('Test Movie 2')];
        $expectedRecommendations = [new Movie('Test Movie 1')];

        $this->movieRepositoryMock->expects($this->once())
            ->method('findAll')
            ->willReturn($movies);

        $this->strategyMock->expects($this->once())
            ->method('getRecommendations')
            ->with($movies)
            ->willReturn($expectedRecommendations);

        $result = $this->service->getRecommendations();

        $this->assertSame($expectedRecommendations, $result);
    }

    public function testGivenNewStrategy_whenSetStrategyIsCalled_thenItShouldUseTheNewStrategyForRecommendations(): void
    {
        $newStrategyMock = $this->createMock(RecommendationStrategyInterface::class);
        $movies = [new Movie('Another Movie')];

        $this->movieRepositoryMock->method('findAll')->willReturn($movies);

        // We expect the new strategy to be used
        $newStrategyMock->expects($this->once())
            ->method('getRecommendations')
            ->with($movies);

        // We expect the original strategy to never be used
        $this->strategyMock->expects($this->never())
            ->method('getRecommendations');

        $this->service->setStrategy($newStrategyMock);
        $this->service->getRecommendations();
    }

    public function testGivenAStrategy_whenGetNameIsCalled_thenItShouldReturnTheStrategyName(): void
    {
        $this->strategyMock->expects($this->once())
            ->method('getName')
            ->willReturn(RandomStrategy::NAME);

        $result = $this->service->getStrategyName();

        $this->assertSame(RandomStrategy::NAME, $result);
    }

    public function testGivenMoviesWithDuplicates_whenGetRecommendationsIsCalled_thenItShouldPassDeduplicatedListToStrategy(): void
    {
        $movie1 = new Movie('Pulp Fiction');
        $movie2 = new Movie('Django');
        $movie3 = new Movie('Pulp Fiction');
        $moviesWithDuplicates = [$movie1, $movie2, $movie3];

        $this->movieRepositoryMock->expects($this->once())
            ->method('findAll')
            ->willReturn($moviesWithDuplicates);

        $expectedDeduplicatedMovies = [$movie1, $movie2];

        $this->strategyMock->expects($this->once())
            ->method('getRecommendations')
            ->with($this->equalTo($expectedDeduplicatedMovies));

        // When: The main method is called.
        $this->service->getRecommendations();
    }
}