phpcs:
	vendor/bin/phpcs

phpstan:
	vendor/bin/phpstan clear-result-cache
	vendor/bin/phpstan analyse

phpcsfixer:
	vendor/bin/php-cs-fixer fix --dry-run --allow-risky=yes

test:
	vendor/bin/phpunit --testdox

infection:
	vendor/bin/infection
