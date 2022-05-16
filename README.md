# What is this

AdminLTE3 bundle docs: https://github.com/kevinpapst/AdminLTEBundle/blob/master/Resources/docs/configurations.md 

> Pet project for my smart home infrastructure.

- [x] Добавить PostgreSQL в .env
- [x] пофиксить .env
- [x] пофиксить .env-local
- [x] добавить PostgreSql в docker-compose.yaml
- [x] sh script for pushing project docker images
- [ ] adminlte3 for symfony - https://github.com/kevinpapst/AdminLTEBundle
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