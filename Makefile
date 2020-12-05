.PHONY: test build cloud

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
		-f ./.cloud/Dockerfile \
		.

push: build
	docker push docker.io/gumbo-millennium/e-voting
