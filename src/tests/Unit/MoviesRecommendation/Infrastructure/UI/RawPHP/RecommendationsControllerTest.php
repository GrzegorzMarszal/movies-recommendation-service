<?php

declare(strict_types=1);

namespace Tests\Unit\MoviesRecommendation\Infrastructure\UI\RawPHP;

use App\MoviesRecommendation\Application\Service\RecommendationService;
use App\MoviesRecommendation\Application\Strategy\RecommendationStrategyInterface;
use App\MoviesRecommendation\Domain\Entity\Movie;
use App\MoviesRecommendation\Domain\Exception\CantFindRecommendationsException;
use App\MoviesRecommendation\Infrastructure\Resolver\StrategyFromKeyResolver;
use App\MoviesRecommendation\Infrastructure\UI\RawPHP\RecommendationsController;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RecommendationsControllerTest extends TestCase
{
    private RecommendationService&MockObject $recommendationServiceMock;
    private StrategyFromKeyResolver&MockObject $strategyResolverMock;
    private RecommendationsController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->recommendationServiceMock = $this->createMock(RecommendationService::class);
        $this->strategyResolverMock = $this->createMock(StrategyFromKeyResolver::class);

        $this->controller = new RecommendationsController(
            $this->recommendationServiceMock,
            $this->strategyResolverMock
        );
    }

    public function testGivenValidStrategyName_whenExecuteIsCalled_thenItResolvesAndSetsStrategyBeforeRendering(): void
    {
        $strategyName = 'random';
        $resolvedStrategyMock = $this->createMock(RecommendationStrategyInterface::class);
        $movies = [new Movie('Pulp Fiction'), new Movie('Django')];

        $this->strategyResolverMock->expects($this->once())
            ->method('resolve')
            ->with($strategyName)
            ->willReturn($resolvedStrategyMock);

        $this->recommendationServiceMock->expects($this->once())
            ->method('setStrategy')
            ->with($resolvedStrategyMock);

        $this->recommendationServiceMock->method('getStrategyName')->willReturn('RANDOM');
        $this->recommendationServiceMock->method('getRecommendations')->willReturn($movies);

        $result = $this->controller->execute($strategyName);

        $expectedString  = 'RANDOM' . PHP_EOL;
        $expectedString .= '-> Pulp Fiction' . PHP_EOL;
        $expectedString .= '-> Django' . PHP_EOL;
        $this->assertSame($expectedString, $result);
    }

    public function testGivenNullStrategyName_whenExecuteIsCalled_thenItSkipsResolvingAndSettingStrategy(): void
    {
        $strategyName = null;
        $movies = [new Movie('Default Movie')];

        $this->strategyResolverMock->expects($this->never())->method('resolve');
        $this->recommendationServiceMock->expects($this->never())->method('setStrategy');

        $this->recommendationServiceMock->method('getStrategyName')->willReturn('DEFAULT');
        $this->recommendationServiceMock->method('getRecommendations')->willReturn($movies);

        $result = $this->controller->execute($strategyName);

        $expectedString  = 'DEFAULT' . PHP_EOL;
        $expectedString .= '-> Default Movie' . PHP_EOL;
        $this->assertSame($expectedString, $result);
    }

    public function testGivenInvalidStrategyName_whenResolverThrowsException_thenItReturnsErrorMessage(): void
    {
        $invalidStrategyName = 'non_existent_strategy';

        $this->strategyResolverMock->expects($this->once())
            ->method('resolve')
            ->with($invalidStrategyName)
            ->willThrowException(new CantFindRecommendationsException());

        $this->recommendationServiceMock->expects($this->never())->method('setStrategy');
        $this->recommendationServiceMock->expects($this->never())->method('getStrategyName');
        $this->recommendationServiceMock->expects($this->never())->method('getRecommendations');

        $result = $this->controller->execute($invalidStrategyName);

        $this->assertSame('Nie można pobrać rekomendacji.' . PHP_EOL, $result);
    }
}