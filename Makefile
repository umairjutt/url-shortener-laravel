.PHONY: up down install migrate seed test fresh logs

up:
	docker compose up -d

down:
	docker compose down

install:
	docker compose exec app composer install
	docker compose exec app php artisan key:generate

migrate:
	docker compose exec app php artisan migrate

seed:
	docker compose exec app php artisan db:seed

fresh:
	docker compose exec app php artisan migrate:fresh --seed

test:
	docker compose exec app ./vendor/bin/pest

logs:
	docker compose logs -f app queue
