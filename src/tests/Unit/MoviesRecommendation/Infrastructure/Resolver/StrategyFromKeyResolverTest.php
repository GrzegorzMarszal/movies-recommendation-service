<?php

declare(strict_types=1);

namespace Tests\Unit\MoviesRecommendation\Infrastructure\Resolver;

use App\MoviesRecommendation\Application\Strategy\RecommendationStrategyInterface;
use App\MoviesRecommendation\Domain\Exception\CantFindRecommendationsException;
use App\MoviesRecommendation\Infrastructure\Resolver\StrategyFromKeyResolver;
use PHPUnit\Framework\TestCase;

class StrategyFromKeyResolverTest extends TestCase
{
    public function testGivenValidKey_whenResolveIsCalled_thenItShouldReturnCorrectStrategy(): void
    {
        $expectedStrategy = $this->createMock(RecommendationStrategyInterface::class);
        $availableStrategies = [
            'random' => $expectedStrategy,
            'other' => $this->createMock(RecommendationStrategyInterface::class),
        ];

        $resolver = new StrategyFromKeyResolver($availableStrategies);
        $resolvedStrategy = $resolver->resolve('random');

        $this->assertSame($expectedStrategy, $resolvedStrategy);
    }

    public function testGivenInvalidKey_whenResolveIsCalled_thenItShouldThrowException(): void
    {
        $availableStrategies = [
            'some_other_strategy' => $this->createMock(RecommendationStrategyInterface::class)
        ];
        $resolver = new StrategyFromKeyResolver($availableStrategies);
        $invalidKey = 'non_existent_strategy';

        $this->expectException(CantFindRecommendationsException::class);
        $this->expectExceptionMessage(sprintf('Strategy "%s" not found', $invalidKey));

        $resolver->resolve($invalidKey);
    }

    public function testGivenEmptyConfiguration_whenResolveIsCalled_thenItShouldThrowException(): void
    {
        $resolver = new StrategyFromKeyResolver([]);
        $anyKey = 'any_key';

        $this->expectException(CantFindRecommendationsException::class);

        $resolver->resolve($anyKey);
    }
}