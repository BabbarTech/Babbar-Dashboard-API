.PHONY: help
-include .env

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(firstword $(MAKEFILE_LIST)) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

start: ## Start the application in the background
	vendor/bin/sail up -d

restart: ## Restart the application
	vendor/bin/sail restart

stop: ## Stop the application
	vendor/bin/sail stop

install: ## Install the application (with Docker)
	scripts/install.sh

update:	## Update application and projets databases
	make migrate

migrate: ## Migrate application and projets databases
	vendor/bin/sail artisan migrate --path=database/migrations/landlord --database=landlord --force
	vendor/bin/sail artisan tenants:artisan "migrate --path=database/migrations/tenant --database=tenant"

migrate_rollback: ## Revert last application and projets databases migration(s)
	vendor/bin/sail artisan tenants:artisan "migrate:rollback --path=database/migrations/tenant --database=tenant"

queue: ## Start application queue worker
	# Launch queues workers
	echo "Start queue worker"
	vendor/bin/sail debug queue:work

dump: ## Dump application and all projets databases
	make artisan_dump
	make artisan_dump_projets

artisan_dump: ## Dump application database
	vendor/bin/sail artisan snapshot:create $(DB_DATABASE)_$(shell date +%Y%m%d-%H%M%S) --compress

artisan_dump_projets: ## Dump all projects databases
	@echo "Creating projets MySQL dumps"...
	vendor/bin/sail artisan project:export

mysqldump_tool:
	vendor/bin/sail exec laravel.test mkdir -p "$(DB_DUMP_DIRECTORY)"
	vendor/bin/sail exec mariadb mysqldump -u $(DB_USERNAME) -h mariadb -p$(DB_PASSWORD) $(DB_DATABASE) | gzip > $(DB_DUMP_DIRECTORY)/$(DB_DATABASE)-$(shell date +%Y%m%d-%H%M%S).sql.gz

check:
	@echo "Process PHPStan"
	make phpstan
	@echo "Process PHPCBF"
	make phpcbf
	@echo "Process PHPCS"
	make phpcs

phpstan:
	vendor/bin/phpstan

phpcs:
	vendor/bin/phpcs

phpcbf:
	vendor/bin/phpcbf
