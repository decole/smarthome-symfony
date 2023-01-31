# MQTT-RABBIT_MQ simple bridge

## Build container

`docker build --tag mqtt-rabbitmq . --file ./docker/Dockerfile`


## Config app


App use queue argument: **it is queue message TTL 60 second**
> args["x-message-ttl"] = int64(60000)

other: see .env file

Mount .env file to docker container

Insert your data for mqtt broker and rabbitMq broker

For example:

```
BROKER_URI="192.168.1.5"
PORT=1883
CLIENT_ID=go_mqtt_client
USER=test
PASSWORD=test
SUBSCRIBE_TOPIC="#"

RABBITMQ_SDN="amqp://rabbit:rabbit@localhost:5673/"
MQTT_QUEUE=mqtt
```

## Start app

start go lang app: `/mqtt-rabbitmq`