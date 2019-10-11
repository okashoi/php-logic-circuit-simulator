run: build
	docker-compose run --rm php
.PHONY: run

build:
	docker-compose build
.PHONY: build

down:
	docker-compose down
.PHNOY: down
