path 		= src
unitpath 	=

help:
	@echo "make phpunit"
	@echo "make phpcs"
	@echo "make lint"

phpunit:
	@phpunit --configuration=phpunit-no-coverage.xml $(unitpath)

phpcs:
	@phpcs --standard=./build/phpcs.xml -p $(path)

phpcs-summery:
	@phpcs --standard=./build/phpcs.xml --report=summary -p $(path)

lint:
	@echo "Syntaxchecker $(path)"
	@find $(path) -name *.php -exec php -l '{}' \; > lint.txt
	@rm lint.txt

unitfiles = $(shell git status -s | sed -r 's/...(.*?)/\1/' | grep ^tests\/unittest\/jamwork\/)
unittests:
	$(foreach unitfile,$(unitfiles), make --no-print-directory phpunit unitpath=$(unitfile);) \