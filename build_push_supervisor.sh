#!/bin/bash

set -ex

# Push supervisor image

export $(xargs < .docker.env)

docker build --no-cache --rm \
    -t registry.gitlab.com/decole/uberserver-symfony:supervisor \
        --build-arg NEW_RELIC_AGENT_VERSION="$NEW_RELIC_AGENT_VERSION" \
        --build-arg NEW_RELIC_LICENSE_KEY="$NEW_RELIC_LICENSE_KEY" \
        --build-arg NEW_RELIC_APPNAME="$NEW_RELIC_APPNAME" \
        --build-arg NEW_RELIC_SUPERVISORNAME="$NEW_RELIC_SUPERVISORNAME" \
        --build-arg NEW_RELIC_DAEMON_ADDRESS="$NEW_RELIC_DAEMON_ADDRESS" \
    . -f docker/php/DockerfileSupervisor

cat ~/.gitlab_access_token.txt | docker login registry.gitlab.com --username decole2014@yandex.ru --password-stdin

docker push registry.gitlab.com/decole/uberserver-symfony:supervisor
