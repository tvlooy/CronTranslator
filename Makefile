.PHONY: all install explain
.RECIPEPREFIX = |

DOCKER_CMD=@docker run -v`pwd`:/data -w /data --user `id -u`:`id -g` -ti intractosre/php:8.2

all:
| @echo "usage: make"
| @echo "  [install]     run composer install"
| @echo "  [explain]     run explain script"

install:
| ${DOCKER_CMD} composer2 install

explain:
| ${DOCKER_CMD} php -dmemory_limit=-1 explain.php

