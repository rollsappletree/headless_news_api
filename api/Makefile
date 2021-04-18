start: ##start docker
	cd docker; docker-compose up -d

stop: ##stop docker
	cd docker; docker-compose stop

down:
	cd docker; docker-compose down

build_docker: ##rebuild docker environment
	cd docker; docker-compose build

bash: ##open a bash into php container
	docker-compose exec php bash

caddy: ##open a bash into php container
	docker-compose exec caddy sh

symfony_install_cli: ##install 'symfony' command from the net
	docker-compose exec php 'wget https://get.symfony.com/cli/installer -O - | bash'

fixtures: ##Run Fixtures
	docker-compose exec php php bin/console hautelook:fixtures:load

create_jwt_secrets:
	docker-compose exec php sh -c '\
        set -e;\
        apk add openssl;\
        mkdir -p config/jwt;\
        jwt_passphrase=$${JWT_PASSPHRASE:-$$(grep ''^JWT_PASSPHRASE='' .env | cut -f 2 -d ''='')};\
        echo "$$jwt_passphrase" | openssl genpkey -out config/jwt/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096;\
        echo "$$jwt_passphrase" | openssl pkey -in config/jwt/private.pem -passin stdin -out config/jwt/public.pem -pubout;\
        setfacl -R -m u:www-data:rX -m u:"$$(whoami)":rwX config/jwt;\
        setfacl -dR -m u:www-data:rX -m u:"$$(whoami)":rwX config/jwt;'

