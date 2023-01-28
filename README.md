# Что это такое

> Pet project умного дома, основанного на ESP8266 и MQTT протоколе

Стадии проекта: [Stages](docs/project/STAGES.md)

Умный дом:
 - MQTT брокер
 - контроллеры с MQTT транспортом
 - серверное приложение (symfony)
 - оповещение при выходе данных датчиков из придела нормы по каналам связи (телеграм/дискорд/колонка с голосовым 
помощником Алиса)

в папке /docs/ находится документация по умному дому и прошивки для контроллеров

Датчики:
 - Сенсоры температуры
 - Сенсоры влажности
 - Реле
 - Датчики движения
 - Датчик дыма аналоговый

## Комментарии

---

[ТЗ по сервису мониторинга доступности контроллеров умного дома](docs/project/DEVICE_CONTROLLER_MONITORING.md)

----

> **Пометка**: пока только датчики из mqtt с передачей детекции дижения всегда, даже кода это деатектировано. 
> Сам сервис понимает в каком он состоянии и как реагировать на сигнал датчиков.

----

> Для prometheus alert manager надо скопировать alertmanager.yml.dist в alertmanager.yml и заменить <TELEGRAM_BOT_TOKEN> на токен вашего бота и свой id чата TELEGRAM_CHAT_ID

## Services

- nginx
- php-fpm - app + supervisor
- postgresql
- redis - cache
- redis-insight - check Redis Data
- supervisor - background process
- rabbitMQ - reactive queue (mqtt, notification)
- CI/CD - Gitlab CI

## Периодические задания:

`php bin/console cli:cron` - команда для активации периодических задач - работает через supervisor, активируется каждую минуту 
нужно создавать критерии для таких задач в папке Domain/PeriodicHandleCriteria/Criteria, смотреть примеры там. 

(фоновые таски будут переписываться)
PeriodicHandleCriteriaCompiler - через dependency injection по сервис тегу регистрируются критерии 
в CriteriaChainService.php  


> Запланирован переход на go сервис, который будет шлюзом между mqtt и rabbitMQ очереди

Работа с mqtt - переведено на очередь RabbitMQ
`bin/console cli:mqtt` - подключено к mqtt и отправка в очередь кролика 
`php bin/console rabbitmq:consumer mqtt_receive_payloads` - слушатель колик по mqtt сообщениям брокера

## Очереди нотификаций:

(переведено на очередь RabbitMQ)

`php bin/console messenger:consume async` - async send emails, telegram, discord message


## История производства проекта на разных фреймворках

Create project by: 
- Yii2 (simple / advanced) [deprecated project]
- Laravel (7/8/9) [deprecated project]
- Symfony (5.4 - current LTS version)


## RTFM:

AdminLte3: https://github.com/ColorlibHQ/AdminLTE/releases/tag/v3.2.0

Symfony Docs: https://symfony.com/doc/5.4/routing.html

Codeception Docs: https://codeception.com/docs/05-UnitTests


### Prometheus:

https://github.com/artprima/prometheus-metrics-bundle

metrics:
 - node_filesystem_avail_bytes - свободное место на проде
 - node_memory_MemFree_bytes - свободный объем оперативки
 - node_procs_blocked - io delay


### Deployment 

https://symfony.com/doc/5.4/deployment.html

```shell
composer install --no-dev --optimize-autoloader
php bin/console cache:clear --env=prod --no-debug
#APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear
```

http://localhost:84/adminlte3/pages/widgets.html - примеры страниц AdminLTE3

https://tomasvotruba.com/blog/2019/03/28/how-to-mock-final-classes-in-phpunit/ - Для тестов - тестировать final classes

https://medium.com/docler-engineering/how-to-make-phpunit-mocking-and-final-classes-cohabit-all-together-ec46c37c3368 - use lib in tests

https://dev.to/daniel_werner/how-to-use-reflection-to-test-private-and-protected-methods-3339 - reflection