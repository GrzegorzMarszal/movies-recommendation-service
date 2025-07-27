<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\MoviesRecommendation\Application\Service\RecommendationService;
use App\MoviesRecommendation\Application\Strategy;
use App\MoviesRecommendation\Infrastructure\Persistence\InMemoryMovieRepository;
use App\MoviesRecommendation\Infrastructure\Resolver\StrategyFromKeyResolver;
use App\MoviesRecommendation\Infrastructure\UI\RawPHP\RecommendationsController;

$movieRepository = new InMemoryMovieRepository(__DIR__ . '/../data/movies.php');
$defaultStrategy = new Strategy\RandomStrategy();

$strategyKey = isset($argv[1]) ? htmlspecialchars($argv[1]) : null;
$recommendationService = new RecommendationService($defaultStrategy, $movieRepository);

$strategyFromKeyResolver = new StrategyFromKeyResolver([
    'weven' => new Strategy\WEvenStrategy(),
    'multiword' => new Strategy\MultiWordStrategy(),
    'random' => new Strategy\RandomStrategy(),
]);

$result = new RecommendationsController($recommendationService, $strategyFromKeyResolver);
echo $result->execute($strategyKey);