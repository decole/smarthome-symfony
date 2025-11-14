# How to deploy a project on your own
0. Install on a server or virtual machine `mosquitto broker` - https://www.vultr.com/docs/install-mosquitto-mqtt-broker-on-ubuntu-20-04-server/
1. Copy `.env` to `.env.local` and customize to your data
    - MQTT_BROKER_URL - specify the ip address of the broker
    - MQTT_PORT - broker port
    - APP_REGISTRATION_ENABLE - flag to enable registration on the site and password recovery
    - MAILER_DSN - пример MAILER_DSN=smtp://login:password@smtp.yandex.ru:465
    - EMAIL_REGISTRATION_EMAIL - enter your email in full
    - EMAIL_REGISTRATION_EMAIL_SUBJECT - registration email subject text
    - EMAIL_RESTORE_SUBJECT - password recovery message subject text
    - TELEGRAM_BOT_TOKEN - telegram bot token
    - DISCORD_WEBHOOK - webhook discord server for project telemetry
    - APP_ENV=dev или `APP_ENV=prod`
    - APP_SCHEME=http
    - APP_HOST=Hostname
    - APP_DEBUG=false or `true`
    - TWO_FACTOR_AUTH=true or `false`
    - API_BASIC_TOKEN=lol123 - token to restrict access to sensor commands through the website or api
    - RABBITMQ_DSN_BRIDGE="amqp://rabbit:rabbit@rabbitmq:5672/" - DSN from the queue, also needed for mqtt-bridge service
    - MQTT_PAYLOADS_RECEIVE_QUEUE=receive_mqtt_payloads - queue to which messages will be pushed mqtt-bridge service
    - CLIENT_ID=go_mqtt_client_dev_stage - for mqtt-bridge service
    - USER=test - for mqtt-bridge service
    - PASSWORD=test - for mqtt-bridge service
    - SUBSCRIBE_TOPIC="#" - for mqtt-bridge service
    - ALICE_QUASAR_COOKIE"mda=0; my=...=; device_id=...; ..." - for smart speaker with Alice
    - ALICE_QUASAR_DEVICE=01c1b1bb-b111-1b1b-11e1-1fd11a1e1a11 - for smart speaker with Alice
    - ALICE_QUASAR_SCENARIO_ID=fbace1ff-11f1-1de1-ad1d-c111a11fba11 - for smart speaker with Alice
2. Copy `docker-compose.yaml.dist` to `docker-compose.yaml` and execute cli command:
    - make build - docker containers will be assembled
    - make up - up your docker-compose environment

3. Then use according to your taste. :)
    - go to <ip>:84
    - registration on site
    - add sensors/relays, sample firmware for NodeMCU in the folder /docs/firmware/Arduino, there is also a folder with libraries that are used for firmware
    - configure pages, which sensors should be displayed on each page
    - close registration - `APP_REGISTRATION_ENABLE=false`
    - use your environment