#!/bin/bash

if [ "$#" -ne 0 ]; then
	docker compose exec -u 1000:1000 php bin/phpunit --filter $*
else
	docker compose exec -u 1000:1000 php bin/phpunit --coverage-html tests/test_coverage/
fi
