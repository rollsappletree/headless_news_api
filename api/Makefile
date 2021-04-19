help: ##Show command list
	@awk -F ':|##' '/^[^\t].+?:.*?##/ {printf "\033[36m%-30s\033[0m %s\n", $$1, $$NF}' $(MAKEFILE_LIST)

start: ##Start docker
	cd docker; docker-compose up -d

stop: ##Stop docker
	cd docker; docker-compose stop

down: ##Stop and delete docker containers
	cd docker; docker-compose down

build_docker: ##Build docker environment
	cd docker; docker-compose build

bash: ##Open a bash into php container
	docker-compose exec php bash

caddy: ##Open a bash into php container
	docker-compose exec caddy sh

build_db: ##Build the DB structure
	docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction

run_fixtures: ##Run fixtures
	docker-compose exec php php bin/console hautelook:fixtures:load

create_jwt_secrets: ##Create private/public key for JWT
	docker-compose exec php sh -c '\
        set -e;\
        apk add openssl;\
        mkdir -p config/jwt;\
        jwt_passphrase=$${JWT_PASSPHRASE:-$$(grep ''^JWT_PASSPHRASE='' .env | cut -f 2 -d ''='')};\
        echo "$$jwt_passphrase" | openssl genpkey -out config/jwt/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096;\
        echo "$$jwt_passphrase" | openssl pkey -in config/jwt/private.pem -passin stdin -out config/jwt/public.pem -pubout;\
        setfacl -R -m u:www-data:rX -m u:"$$(whoami)":rwX config/jwt;\
        setfacl -dR -m u:www-data:rX -m u:"$$(whoami)":rwX config/jwt;'

run_tests: ##Run tests
	docker-compose exec php bin/phpunit --testdox

first_run: ##First run command - install and set up all the stuff
	make build_docker && make start && make create_jwt_secrets && make build_db && make run_fixtures

cs_fix:
	docker-compose exec php php vendor/bin/php-cs-fixer fix
