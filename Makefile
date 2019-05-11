COMPOSE_PROJECT_NAME?=slowloris
COMPOSE_FILE?=docker/dev/docker-compose.yml

run:
	docker-compose -p ${COMPOSE_PROJECT_NAME} -f ${COMPOSE_FILE} up -d

restart:
	docker-compose -p ${COMPOSE_PROJECT_NAME} -f ${COMPOSE_FILE} restart -t 0

rm: stop
	docker-compose -p ${COMPOSE_PROJECT_NAME} -f ${COMPOSE_FILE} rm -fv

stop:
	docker-compose -p ${COMPOSE_PROJECT_NAME} -f ${COMPOSE_FILE} stop -t 0

attack-get:
	docker exec -it ${COMPOSE_PROJECT_NAME}-atacker-1 php /code/src/slowlories-script.php get 1000 victim.localhost 80

attack-post:
	docker exec -it ${COMPOSE_PROJECT_NAME}-atacker-1 php /code/src/slowlories-script.php post 1000 victim.localhost 80

attack-rand:
	docker exec -it ${COMPOSE_PROJECT_NAME}-atacker-1 php /code/src/slowlories-script.php random 1000 victim.localhost 80

show-response-time:
	src/response-time.bash

logs:
	docker-compose -p ${COMPOSE_PROJECT_NAME} -f ${COMPOSE_FILE} logs -f ${SERVICE}

build-protected:
	docker-compose -f ${COMPOSE_FILE} build apache-protected attacker

build-dummy:
	docker-compose -f ${COMPOSE_FILE} build apache-dummy attacker

run-protected:
	docker-compose -p ${COMPOSE_PROJECT_NAME} -f ${COMPOSE_FILE} up -d apache-protected
	docker-compose -p ${COMPOSE_PROJECT_NAME} -f ${COMPOSE_FILE} up -d --scale attacker=5 attacker

run-dummy:
	docker-compose -p ${COMPOSE_PROJECT_NAME} -f ${COMPOSE_FILE} up -d apache-dummy
	docker-compose -p ${COMPOSE_PROJECT_NAME} -f ${COMPOSE_FILE} up -d --scale attacker=5  attacker

ps:
	docker-compose -p ${COMPOSE_PROJECT_NAME} -f ${COMPOSE_FILE} ps
