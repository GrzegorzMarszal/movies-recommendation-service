<?php

declare(strict_types=1);

namespace Tests\Unit\MoviesRecommendation\Infrastructure\Persistence;

use App\MoviesRecommendation\Domain\Entity\Movie;
use App\MoviesRecommendation\Infrastructure\Persistence\InMemoryMovieRepository;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class InMemoryMovieRepositoryTest extends TestCase
{
    private ?string $tempMovieFilePath = null;

    protected function tearDown(): void
    {
        // Clean up the temporary file after each test
        if ($this->tempMovieFilePath && file_exists($this->tempMovieFilePath)) {
            unlink($this->tempMovieFilePath);
            $this->tempMovieFilePath = null;
        }
        parent::tearDown();
    }

    public function testGivenValidMovieFile_whenRepositoryIsCreated_thenItShouldLoadMoviesCorrectly(): void
    {
        $this->createTempMovieFile("<?php \$movies = ['Pulp Fiction', 'Django'];");

        $repository = new InMemoryMovieRepository($this->tempMovieFilePath);
        /** @var Movie[] $movies */
        $movies = $repository->findAll();

        $this->assertCount(2, $movies);
        $this->assertInstanceOf(Movie::class, $movies[0]);
        $this->assertSame('Pulp Fiction', $movies[0]->getTitle());
        $this->assertSame('Django', $movies[1]->getTitle());
    }

    public function testGivenNonExistentFile_whenRepositoryIsCreated_thenItShouldThrowException(): void
    {
        $nonExistentFile = '/path/to/non/existent/file.php';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('File "%s" not found', $nonExistentFile));

        new InMemoryMovieRepository($nonExistentFile);
    }

    public function testGivenFileWithMixedDataTypes_whenRepositoryIsCreated_thenItShouldLoadOnlyStringMovies(): void
    {
        $this->createTempMovieFile("<?php \$movies = ['Pulp Fiction', 123, null, 'Django'];");

        $repository = new InMemoryMovieRepository($this->tempMovieFilePath);
        /** @var Movie[] $movies */
        $movies = array_values($repository->findAll());

        $this->assertCount(2, $movies, 'It should filter out non-string values.');
        $this->assertSame('Pulp Fiction', $movies[0]->getTitle());
        $this->assertSame('Django', $movies[1]->getTitle());
    }

    public function testGivenFileWithUntrimmedTitles_whenRepositoryIsCreated_thenItShouldReturnTrimmedTitles(): void
    {
        $this->createTempMovieFile("<?php \$movies = ['  Pulp Fiction  ', ' Django '];");

        $repository = new InMemoryMovieRepository($this->tempMovieFilePath);
        /** @var Movie[] $movies */
        $movies = $repository->findAll();

        $this->assertCount(2, $movies);
        $this->assertSame('Pulp Fiction', $movies[0]->getTitle(), 'It should trim whitespace from the title.');
        $this->assertSame('Django', $movies[1]->getTitle());
    }

    private function createTempMovieFile(string $content): void
    {
        // Create a temporary file with unique name for the test
        $this->tempMovieFilePath = tempnam(sys_get_temp_dir(), 'movies_test');
        file_put_contents($this->tempMovieFilePath, $content);
    }
}