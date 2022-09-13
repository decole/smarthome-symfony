#!/bin/bash

set -ex

# Push app image

export $(xargs < .docker.env)

docker build --no-cache --rm -t registry.gitlab.com/decole/uberserver-symfony:app . -f docker/php/DockerfileApp

cat ~/.gitlab_access_token.txt | docker login registry.gitlab.com --username decole2014@yandex.ru --password-stdin

docker push registry.gitlab.com/decole/uberserver-symfony:app
