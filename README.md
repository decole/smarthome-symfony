# Что это такое

> Pet project для моего домашнего умного дома, основанного на ESP8266 и MQTT протоколе

Стадии проекта: [Stages](docs/project/STAGES.md)

SSL для локального проекта или reverse-proxy на production [custom SSL](docs/project/SSL.md)

в папке /docs/ находится документация по умному дому и прошивки для контроллеров

## Комментарии

----

> **Пометка**: пока только датчики из mqtt с передачей детекции дижения всегда, даже кода это деатектировано. 
> Сам сервис понимает в каком он состоянии и как реагировать на сигнал датчиков.

----

> Для prometheus alert manager надо скопировать alertmanager.yml.dist в alertmanager.yml и заменить <TELEGRAM_BOT_TOKEN> на токен вашего бота и свой id чата TELEGRAM_CHAT_ID

## Services

- nginx
- php-fpm - app
- php-cli - supervisor
- postgresql
- redis - cache
- redis-insight - check Redis Data
- supervisor
- rabbitMQ - reactive queue
- логирование - логирование пока в файлики var/log проекта
- мониторинг - Prometheus
- CI/CD

## Периодические задания:

`php bin/console cli:cron` - команда для активации бесконечного цикла периодических задач
нужно создавать критерии для таких задач в папке Domain/PeriodicHandleCriteria/Criteria, смотреть примеры там. 

PeriodicHandleCriteriaCompiler - через dependency injection по сервис тегу регистрируются критерии 
в CriteriaChainService.php  


## Очереди нотификаций:

`php bin/console messenger:consume async` - async send emails, telegram, discord message

работает через контейнер supervisor


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