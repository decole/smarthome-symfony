# What it is

> Pet project of a smart home based on ESP8266 and MQTT protocol

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


## Периодические задания

`php bin/console cli:cron` - команда для активации периодических задач - работает через supervisor, активируется каждую минуту 
нужно создавать критерии для таких задач в папке Domain/PeriodicHandleCriteria/Criteria, смотреть примеры там. 

(фоновые таски будут переписываться)
PeriodicHandleCriteriaCompiler - через dependency injection по сервис тегу регистрируются критерии 
в CriteriaChainService.php  


## MQTT

Working with mqtt - translated to RabbitMQ queue
Connection to mqtt happens through a separate go lang service, it creates messages in the rabbit task queue
`php bin/console rabbitmq:consumer mqtt_receive_payloads` - listener by mqtt messages broker


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

http://localhost:84/adminlte3/pages/widgets.html - примеры страниц AdminLTE3

https://tomasvotruba.com/blog/2019/03/28/how-to-mock-final-classes-in-phpunit/ - Для тестов - тестировать final classes

https://medium.com/docler-engineering/how-to-make-phpunit-mocking-and-final-classes-cohabit-all-together-ec46c37c3368 - use lib in tests

https://dev.to/daniel_werner/how-to-use-reflection-to-test-private-and-protected-methods-3339 - reflection