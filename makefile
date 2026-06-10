# Define container names
APP_CONTAINER=app
NGINX_CONTAINER=nginx
MYSQL_CONTAINER=mysql
REDIS_CONTAINER=redis

# Define dev override file
DEV_COMPOSE=docker-compose.override.yml

# Extract MySQL credentials from .env file
MYSQL_DB=$(shell grep DB_DATABASE .env | cut -d '=' -f2)
MYSQL_USER=$(shell grep DB_USERNAME .env | cut -d '=' -f2)
MYSQL_PASSWORD=$(shell grep DB_PASSWORD .env | cut -d '=' -f2)

DOCKER_COMPOSE=docker compose

.PHONY: help up prod stop down restart restart-container stop-container artisan composer npm logs bash psql redis test

## 📜 Display all available commands
help:
	@echo ""
	@echo "🔥  Available Makefile Commands 🔥"
	@echo "--------------------------------------------------------------"
	@echo "💻  Start environment:"
	@echo "  make up             - Start the DEV environment"
	@echo "  make prod           - Start the PROD environment"
	@echo ""
	@echo "🛑  Stop and manage containers:"
	@echo "  make stop           - Stop all containers"
	@echo "  make down           - Remove all containers and volumes"
	@echo "  make restart        - Restart all containers"
	@echo "  make restart-container CONTAINER=<name> - Restart a specific container"
	@echo "  make stop-container CONTAINER=<name>    - Stop a specific container"
	@echo ""
	@echo "🎭  Artisan commands:"
	@echo "  make artisan <cmd>  - Run 'php artisan <cmd>' inside the app container"
	@echo ""
	@echo "👀  PHPUnit:"
	@echo "  make test  - Run PHPUnit tests"
	@echo ""
	@echo "🎼  Composer commands:"
	@echo "  make composer <cmd> - Run 'composer <cmd>' inside the app container"
	@echo ""
	@echo "🎸  NPM commands:"
	@echo "  make npm <cmd>      - Run 'npm <cmd>' inside the app container"
	@echo ""
	@echo "🖥  Open container shell:"
	@echo "  make bash           - Open a bash shell inside the app container"
	@echo ""
	@echo "📜  Logs:"
	@echo "  make logs <container> - View logs of a specific container"
	@echo ""
	@echo "🐬  MySQL CLI:"
	@echo "  make mysql          - Open mysql shell with credentials from .env"
	@echo ""
	@echo "🔥  Redis CLI:"
	@echo "  make redis          - Open redis-cli inside the Redis container"
	@echo ""

## 💻 Start the DEV environment (with override)
up:
	$(DOCKER_COMPOSE) -f docker-compose.yml -f $(DEV_COMPOSE) up -d

## 💻 Start the PROD environment (without override)
prod:
	$(DOCKER_COMPOSE) -f docker-compose.yml up -d

## 🛑 Stop all running containers
stop:
	$(DOCKER_COMPOSE) stop

## 🗑 Remove all containers and volumes
down:
	$(DOCKER_COMPOSE) down -v

## 🔄 Restart all containers
restart:
	$(DOCKER_COMPOSE) restart

## 🔄 Restart a specific container (usage: make restart-container CONTAINER=nginx)
restart-container:
	$(DOCKER_COMPOSE) restart $(CONTAINER)

## 🛑 Stop a specific container (usage: make stop-container CONTAINER=postgres)
stop-container:
	$(DOCKER_COMPOSE) stop $(CONTAINER)

## 🎭 Run PHP inside the app container (usage: make php -v)
php:
	$(DOCKER_COMPOSE) exec -u www-data $(APP_CONTAINER) php $(filter-out $@,$(MAKECMDGOALS))

## 🎭 Run an Artisan command (usage: make artisan migrate)
artisan:
	$(DOCKER_COMPOSE) exec -u www-data $(APP_CONTAINER) php artisan $(filter-out $@,$(MAKECMDGOALS))

## 🎼 Run a Composer command (usage: make composer install)
composer:
	$(DOCKER_COMPOSE) exec -u www-data $(APP_CONTAINER) composer $(filter-out $@,$(MAKECMDGOALS))

## 🎸 Run an NPM command (usage: make npm run dev)
npm:
	$(DOCKER_COMPOSE) exec -u www-data $(APP_CONTAINER) npm $(filter-out $@,$(MAKECMDGOALS))

## 🖥 Open a bash shell inside the app container
bash:
	$(DOCKER_COMPOSE) exec -u www-data $(APP_CONTAINER) bash

## 📜 View logs of a specific container (usage: make logs nginx)
logs:
	$(DOCKER_COMPOSE) logs -f $(filter-out $@,$(MAKECMDGOALS))

## 🐬 Open MySQL shell with credentials from .env
mysql:
	$(DOCKER_COMPOSE) exec $(MYSQL_CONTAINER) mysql -u$(MYSQL_USER) -p$(MYSQL_PASSWORD) $(MYSQL_DB)

## 🔥 Open Redis CLI inside the Redis container
redis:
	$(DOCKER_COMPOSE) exec $(REDIS_CONTAINER) redis-cli

## 🧪 Run PHPUnit tests
test:
	$(DOCKER_COMPOSE) exec -u www-data $(APP_CONTAINER) php artisan test

## Fix for make to avoid creating unnecessary files
%:
	@:
