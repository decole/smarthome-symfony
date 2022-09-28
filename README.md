# What is this

> Pet project for my smart home infrastructure.


### First stage (MVP)

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
- [x] Telegram notification
- [x] Email notification
- [x] Constructor data pages CRUD
- [x] Mqtt service validation - listen mqtt devices


### Second stage

- [ ] Api service validation - validate devices with api interface
- [ ] Http service validation - monitoring uptime
- [ ] Alice Smart Home CRUD
- [ ] Alice Skill CRUD
- [ ] Alice Notify Alerts System CRUD
- [ ] Api service validation
- [ ] Http service validation
- [x] Log application
- [ ] Log services
- [ ] Grafana
- [ ] Prometheus alerting by application and services
- [ ] CI
- [ ] CD
- [ ] Telegram bot

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
- redis
- redis-insight
- supervisor
- rabbitMQ
- логирование - логирование пока в файлики var/log проекта
- мониторинг - Prometheus
- ~~CI/CD~~ - как только будет (MVP) Minimal version product, так сразу прикручу

## Периодические задания:

`php bin/console cli:cron` - команда для активации бесконечного цикла периодических задач
нужно создавать критерии для таких задач в папке Domain/PeriodicHandleCriteria/Criteria, смотреть примеры там. 

PeriodicHandleCriteriaCompiler - через dependency injection по сервис тегу регистрируются критерии 
в CriteriaChainService.php  


## Queue:

`php bin/console messenger:consume async` - async send emails, telegram, discord message

работает через контейнер supervisor


## History

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


