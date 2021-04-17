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
