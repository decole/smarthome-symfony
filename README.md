# What is this

AdminLte3: https://github.com/ColorlibHQ/AdminLTE/releases/tag/v3.2.0


Symfony Docs: https://symfony.com/doc/5.4/routing.html

> Pet project for my smart home infrastructure.

- [x] Добавить PostgreSQL в .env
- [x] пофиксить .env
- [x] пофиксить .env-local
- [x] добавить PostgreSql в docker-compose.yaml
- [x] sh script for pushing project docker images
- [x] add adminlte3 distributive
- [ ] mqtt service
- [ ] auth system 
- [ ]


----

На проде и на разраб стенде docker-compose.yaml файлы разные будут, поэтому нужно
скопировать docker-compose.yaml.dist в docker-compose.yaml и в нем настраивать все под
свои нужды.

## Services

- nginx
- php-fpm - app
- php-cli - supervisor
- postgresql


## History

Create project by: 
- Yii2 (simple / advanced) 
- Laravel (7/8/9)
- Symfony (current version)


## Список задействованных ключей кэша
    - topicsByType - тип устройства и задействованные топики


## Queue:

php bin/console messenger:consume async - async send emails

в последствии нужно будет сделать задачу в supervisor чтобы отправлять письма