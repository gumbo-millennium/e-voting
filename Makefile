.PHONY: test build cloud
PORT = 8080

test:
	yarn install
	yarn build
	composer install -ao
	vendor/bin/phpcs
	vendor/bin/phpunit

build:
	yarn install
	yarn run build
	composer install --no-dev -ao
	docker build \
		-t gumbo-millennium/e-voting \
		-t docker.io/gumbo-millennium/e-voting \
		.

push: build
	docker push docker.io/gumbo-millennium/e-voting

launch:
	docker container rm --force gumbo-evoting-dev || true
	docker-compose up -d
	docker run \
		--name gumbo-evoting-dev \
		--publish 127.0.0.1:9090:$(PORT) \
		--env PORT=$(PORT) \
		--network evoting_network \
		docker.io/gumbo-millennium/e-voting
