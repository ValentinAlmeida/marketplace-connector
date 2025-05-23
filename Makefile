# Constantes
SERVER=marketplace-connector
DATABASE=db-marketplace
REDIS=redis
BEAUTIFUL_CERF=mockoon-service

.PHONY: help test start serve queue stop restart cache migration migrationf seed-generate fresh migrate rollback seed key-generate create-users

help:
	@echo "Available targets:"
	@echo "  make help           - Show this help message"
	@echo "  make test           - Run tests"
	@echo "  make serve          - Start the Laravel server using Docker"
	@echo "  make start          - Start the Laravel server using Docker"
	@echo "  make queue          - Start the Laravel queue listener"
	@echo "  make stop           - Stop all Docker containers"
	@echo "  make cache          - Clear and cache configuration, routes, and views"
	@echo "  make restart        - Restart all Docker containers"
	@echo "  make migration NAME=<table_name> - Create a migration for a new table"
	@echo "  make fresh          - Run fresh migrations"
	@echo "  make migrate        - Run migrations"
	@echo "  make rollback       - Rollback migrations"
	@echo "  make seed           - Seed database"
	@echo "  make key-generate   - Generate JWT key"
	@echo "  make create-users   - Create users (specify q=quantity)"

test:
	@echo "Running tests..."
	@if [ ! -z "$(SERVER)" ]; then \
	    docker exec $(SERVER) bash -c "php artisan test"; \
	fi

fresh:
	@echo "Running fresh database migrations..."
	@if [ ! -z "$(SERVER)" ]; then \
	    docker exec $(SERVER) bash -c "php artisan migrate:fresh"; \
	fi

migrate:
	@echo "Running migrations..."
	@if [ ! -z "$(SERVER)" ]; then \
	    docker exec $(SERVER) bash -c "php artisan migrate"; \
	fi

rollback:
	@echo "Running rollback migrations..."
	@if [ ! -z "$(SERVER)" ]; then \
	    docker exec $(SERVER) bash -c "php artisan migrate:rollback"; \
	fi

serve:
	@echo "Starting Laravel server..."
	@if [ ! -z "$(SERVER)" ]; then \
		docker exec -d $(SERVER) bash -c "php artisan serve --host 0.0.0.0"; \
		echo "Server started..."; \
	fi

seed:
	@echo "Seeding database"
	@if [ ! -z "$(SERVER)" ]; then \
		docker exec -d $(SERVER) bash -c "php artisan db:seed"; \
		echo "Database ready..."; \
	fi

start:
	@echo "Starting backend server and services..."
	@if [ ! -z "$(SERVER)" ]; then \
		docker stop $(SERVER); \
		docker start $(DATABASE); \
		docker start $(REDIS); \
		docker start $(BEAUTIFUL_CERF); \
		docker start $(SERVER); \
		make serve; \
	fi

queue:
	@echo "Starting queue listener..."
	@if [ ! -z "$(SERVER)" ]; then \
		docker exec $(SERVER) bash -c "php artisan queue:listen"; \
	fi

key-generate:
	@echo "Create key jwt..."
	@if [ ! -z "$(SERVER)" ]; then \
		docker exec $(SERVER) bash -c "php artisan jwt:generate"; \
	fi

stop:
	@echo "Stopping all servers and services..."
	@if [ ! -z "$(SERVER)" ]; then docker stop $(SERVER); fi
	@if [ ! -z "$(DATABASE)" ]; then docker stop $(DATABASE); fi
	@if [ ! -z "$(REDIS)" ]; then docker stop $(REDIS); fi
	@if [ ! -z "$(BEAUTIFUL_CERF)" ]; then docker stop $(BEAUTIFUL_CERF); fi

cache:
	@echo "Clearing and caching configuration..."
	@if [ ! -z "$(SERVER)" ]; then \
		docker exec $(SERVER) bash -c "php artisan config:cache && php artisan route:cache && php artisan optimize && php artisan view:clear && composer dump-autoload"; \
	fi

migration:
ifndef n
	$(error You must specify a NAME for the table, e.g., make migration n=example)
endif
	@echo "Creating migration..."
	@if [ ! -z "$(SERVER)" ]; then \
		docker exec $(SERVER) bash -c "php artisan make:migration create_${n}_table --create=${n}"; \
	fi

migrationf:
ifndef n
	$(error You must specify a NAME for the table, e.g., make migrationf n=example)
endif
	@echo "Creating migration with fields..."
	@if [ ! -z "$(SERVER)" ]; then \
		docker exec $(SERVER) bash -c "php artisan make:migration add_fields_${n}_table --create=${n}"; \
	fi

create-users:
ifndef q
	$(error You must specify a QUANTITY of users, e.g., make create-users q=10)
endif
	@echo "Creating users..."
	@if [ ! -z "$(SERVER)" ]; then \
		docker exec $(SERVER) bash -c "php artisan factory:users $(q)"; \
	fi

seed-generate:
ifndef n
	$(error You must specify a NAME for the seeder, e.g., make seed-generate n=example)
endif
	@echo "Generating seeder..."
	@if [ ! -z "$(SERVER)" ]; then \
		docker exec $(SERVER) bash -c "php artisan make:seeder ${n}Seeder"; \
	fi

restart: stop start