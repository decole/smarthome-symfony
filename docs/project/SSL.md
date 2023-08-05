Used this guide

https://www.humankode.com/ssl/create-a-selfsigned-certificate-for-nginx-in-5-minutes/

```shell
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout localhost.key -out localhost.crt -config localhost.conf
```

you need to specify port 443 in docker-compose.yml


Example:

```yaml
services:
  nginx:
    image: nginx:alpine
    container_name: uberserver-web
#    restart: always
    volumes:
      - ./:/var/www
      - ./docker/nginx/config:/etc/nginx/conf.d
      - ./docker/nginx/logs:/var/log/nginx
      - ./docker/ssl/localhost.crt:/etc/ssl/certs/localhost.crt
      - ./docker/ssl/localhost.key:/etc/ssl/private/localhost.key
    depends_on:
      - php-fpm
    ports:
      - "84:80"
      - "444:443"
    networks:
      - uberserver
```

will be available at https://uberserver.local:444/

don't forget to put in /etc/hosts

```shell
127.0.0.1       uberserver.local
```
