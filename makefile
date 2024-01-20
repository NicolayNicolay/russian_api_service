include .env

up:
	docker compose up -d

rebuild:
	docker compose up -d --build

stop:
	docker compose stop

shell:
	docker exec -it $$(docker ps -q -f name=ubuntu.${APP_NAMESPACE}) bash

up-front:
	docker exec -it $$(docker ps -q -f name=ubuntu.${APP_NAMESPACE}) npm run prod

up-front-dev:
	docker exec -it $$(docker ps -q -f name=ubuntu.${APP_NAMESPACE}) npm run watch