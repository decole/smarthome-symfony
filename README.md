# What is this

> Pet project for my smart home infrastructure.

### First stage

- [x] Добавить PostgreSQL в .env
- [x] пофиксить .env
- [x] пофиксить .env-local
- [x] добавить PostgreSql в docker-compose.yaml
- [x] sh script for pushing project docker images
- [x] add adminlte3 distributive
- [x] mqtt service
- [x] auth system 
- [x] abstract CRUD factory
- [x] sensor CRUD
- [x] relay CRUD
- [x] Security CRUD
- [x] Fire-secure CRUD
- [x] Notification CRUD
- [ ] Alice Smart Home CRUD
- [ ] Alice Skill CRUD
- [ ] Alice Notify Alerts System CRUD
- [ ] Constructor data pages CRUD
- [ ] Mqtt service validation
- [ ] Api service validation
- [ ] Http service validation

----

> **Пометка**: пока только датчики из mqtt с передачей детекции дижения всегда, даже кода это деатектировано. 
> Сам сервис понимает в каком он состоянии и как реагировать на сигнал датчиков.

----

**На проде и на разраб стенде docker-compose.yaml файлы разные будут, поэтому нужно
скопировать docker-compose.yaml.dist в docker-compose.yaml и в нем настраивать все под
свои нужды.**


## Services

- nginx
- php-fpm - app
- php-cli - supervisor
- postgresql
- redis
- redis-insight
- supervisor
- rabbitMQ
- ~~логирование~~
- ~~мониторинг~~
- ~~CI/CD~~


## Список задействованных ключей кэша
    - topicsByType - тип устройства и задействованные топики


## Queue:

php bin/console messenger:consume async - async send emails

в последствии нужно будет сделать задачу в supervisor чтобы отправлять письма


## History

Create project by: 
- Yii2 (simple / advanced) 
- Laravel (7/8/9)
- Symfony (5.4 - current LTS version)


## RTFM:

AdminLte3: https://github.com/ColorlibHQ/AdminLTE/releases/tag/v3.2.0

Symfony Docs: https://symfony.com/doc/5.4/routing.html

Codeception Docs: https://codeception.com/docs/05-UnitTests



### Prometheus:

https://github.com/artprima/prometheus-metrics-bundle