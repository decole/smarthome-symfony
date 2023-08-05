[![pipeline status](https://gitlab.com/decole/uberserver-symfony/badges/master/pipeline.svg)](https://gitlab.com/decole/uberserver-symfony/-/commits/master)

[![Latest Release](https://gitlab.com/decole/uberserver-symfony/-/badges/release.svg)](https://gitlab.com/decole/uberserver-symfony/-/releases)


# What it is?

## Pet project of a smart home based on ESP8266 and MQTT protocol

Smart Home:
 - MQTT broker
 - controllers with MQTT transport
 - backend application (symfony), frontend - twig + jquery
 - notification when the sensor data goes out of the aisle of the norm via communication channels (telegram / discord / column with the voice assistant Alice)

Folder `/docs/` contains documentation on smart home and firmware for controllers

Devices:
 - Temperature sensors
 - Humidity sensors
 - Relay
 - Motion sensors
 - Analog smoke detector
 - Dry contact sensor
 - Leakage sensors


## Description

- [Start and configure.md](docs%2Fproject%2FStart%20and%20configure.md) 
- [Work process.md](docs%2Fproject%2FWork%20process.md)

---

[Terms of reference for the service for monitoring the availability of smart home controllers](docs/project/DEVICE_CONTROLLER_MONITORING.md)

----

> **Tag**: so far only sensors from mqtt with movement detection transmission always, even the code is de-detected. 
> The service itself understands what state it is in and how to respond to the sensor signal.


## Services

- nginx
- php-fpm
- postgresql
- redis
- redis-insight
- supervisor
- rabbitMQ
- CI/CD - Gitlab CI

## Changing the TimeZone for your

See in `/docker/php/php-fpm.ini` parameter `date.timezone`

## Periodic tasks

An external cron will call the `make cron` command once a minute, which will pull the `cli:schedule:run` command
inside the same way, the internal dispatcher from the database will call periodic jobs. For example, you can make a 
command, which goes to your calendar once an hour and if you have something written down soon, it will send a message 
to telegram.

The command below can be called from an external cron, or added to add to the database as a task that runs every minute
and call `make cron` 

`php bin/console cli:cron` - command to activate periodic tasks - works through supervisor.


## MQTT

Working with mqtt - translated to RabbitMQ queue
Connection to mqtt happens through a separate go lang service, it creates messages in the rabbit task queue
`php bin/console rabbitmq:consumer mqtt_receive_payloads` - listener by mqtt messages broker

[ ! ] do not forget in **.env.local** file in development environment and on your production server to have
different **CLIENT_ID** - otherwise there will be an error connecting to the mqtt broker.


## Notification queues

`php bin/console messenger:consume async` - async send emails, telegram, discord message by RabbitMQ


## History of project production on different frameworks

Create project by: 
- Yii2 (simple / advanced) [deprecated project]
- Laravel (7/8/9) [deprecated project]
- Symfony (5.4 - current LTS version)


## RTFM:

AdminLte3: https://github.com/ColorlibHQ/AdminLTE/releases/tag/v3.2.0

Symfony Docs: https://symfony.com/doc/5.4/routing.html

Codeception Docs: https://codeception.com/docs/05-UnitTests

Example pages at AdminLTE3: http://localhost:84/adminlte3/pages/widgets.html

Tests by final classes: https://tomasvotruba.com/blog/2019/03/28/how-to-mock-final-classes-in-phpunit/

Use lib in tests by final classes: https://medium.com/docler-engineering/how-to-make-phpunit-mocking-and-final-classes-cohabit-all-together-ec46c37c3368

Reflection: https://dev.to/daniel_werner/how-to-use-reflection-to-test-private-and-protected-methods-3339

Google 2fa: https://github.com/antonioribeiro/google2fa

QR code gen: https://github.com/antonioribeiro/google2fa-qrcode