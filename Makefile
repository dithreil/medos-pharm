check_back:
	./vendor/bin/phpunit tests/*
	./vendor/bin/phplint
	./vendor/bin/phpcs -n
	./vendor/bin/phpstan analyse

fix_back:
	./vendor/bin/phpcbf

check_front:
	yarn eslint
	yarn stylelint
