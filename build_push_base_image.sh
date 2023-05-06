#!/bin/bash

set -ex

# Push base app image

docker build --no-cache --rm -t registry.gitlab.com/decole/uberserver-symfony:base . -f docker/php/DockerfileBase

cat ~/.gitlab_access_token.txt | docker login registry.gitlab.com --username decole2014@yandex.ru --password-stdin

docker push registry.gitlab.com/decole/uberserver-symfony:base
