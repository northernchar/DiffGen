install:
	composer install
validate:
	composer validate
dump:
	composer dump-autoload
lint: 
	composer exec --verbose phpcs -- --standard=PSR12 src bin
test:
	phpunit tests
push:
	git add .
	git commit -m "fix"
	git push