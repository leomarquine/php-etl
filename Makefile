phpcs:
	vendor/bin/phpcs -n

phpcbf:
	vendor/bin/phpcbf

phpstan:
	vendor/bin/phpstan clear-result-cache
	vendor/bin/phpstan analyse

phpcsfixer:
	vendor/bin/php-cs-fixer fix --dry-run --allow-risky=yes --diff

test:
	vendor/bin/phpunit --testdox

infection:
	vendor/bin/infection
