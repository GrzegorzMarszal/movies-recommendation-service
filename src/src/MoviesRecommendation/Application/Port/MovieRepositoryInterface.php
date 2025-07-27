<?php

declare(strict_types = 1);

namespace App\MoviesRecommendation\Application\Port;

use App\MoviesRecommendation\Domain\Entity\Movie;

interface MovieRepositoryInterface
{
    /**
     * @return Movie[]
     */
    public function findAll(): array;
}