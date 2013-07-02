path 		= src
unitpath 	=

help:
	@echo "make phpunit"
	@echo "make phpcs"
	@echo "make lint"

phpunit:
	@phpunit $(unitpath)

phpcs:
	@phpcs --standard=./build/phpcs.xml --report=summary -p $(path)

lint:
	@echo "Syntaxchecker $(path)"
	@find $(path) -name *.php -exec php -l '{}' \; > lint.txt
	@rm lint.txt
