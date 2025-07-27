environment-build:
	docker compose down; docker-compose up --build -d
environment-start:
	docker compose up -d
build-application:
	docker compose exec php bash -c "composer install"
run-tests:
	docker compose exec php bash -c "php  vendor/bin/phpunit --testsuite Unit --testdox"
