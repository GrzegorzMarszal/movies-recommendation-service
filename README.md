# Movies Recommendation Service Manual

## Setup environment

1. Build environment:
   ```bash
   make environment-build
   ```
2. Start environment:
   ```bash
   make environment-start
   ```
   
3. Install dependencies:
   ```bash
   make build-application
   ```

## Run tests

   ```bash
   make run-tests
   ```

## Application Usage
This is a simple application to recommend movies based on user preferences. 

We use just CLI to show the application in action.

You can start it by diving into the PHP container

   ```bash
   docker compose exec -it php bash
   ```

and running the following command:

   ```bash
  php public/index.php [REVOMMENDATION_TYPE]
   ```
Where available recommendation types are:
   * `weven` - Wszystkie filmy na literę W, ale tylko jeśli mają parzystą liczbę znaków w tytule
   * `random` - 3 losowe tytuły
   * `multiword` - Wszystkie tytuły, które składają się z więcej niż 1 słowa

You can also skip the argument and it will default to `random`


### Example
Command:
   ```bash
   php public/index.php weven
   ```
Result:
   ```text
   Wszystkie filmy na literę W, ale tylko jeśli mają parzystą liczbę znaków w tytule
   -> Whiplash
   -> Wyspa tajemnic
   -> Władca Pierścieni: Drużyna Pierścienia
   ``` 

## Extend Recommendation Strategies

If you want to add your own recommendation strategy, you can do it by:
 * Creating a new class in the `src/MoviesRecommendation/Application/Strategy` (it has to implement the `RecommendationStrategyInterface` interface)
 * Then you just need to inject it into the `RecommendationService` class as a constructor dependency.