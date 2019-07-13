# Architecture du projet API RUN P19
A sample dockerized multi-site.

![bckg](/content/images/bckg.png)

## Required
* Docker
* (Optional) several domain registred in a DNS
## Services
You can display 6 services :
* https://ghost.my-own-domain.com : Ghost blog.
* http://traefik.ghost.my-own-domain.com : Traefik dashboard.
* http://traefik.my-own-domain.com:3000 : Grafana dashboard. 
* https://web.my-own-domain.com : A simple PHP + PostgreSQL website
* https://nextcloud.my-own-domain.com : Nextcloud.
* https://adminer.my-own-domain.com : A Adminer dashboard which will allow you to manage the 3 databases of the project.

## Structure
### Files
You can find below the project's structure :

~~~
├── bdd
│   └── web
├── docker-compose.yml
├── ghost
│   └── blog
├── nextcloud
│   ├── app
│   ├── config
│   ├── db
│   ├── db.env
│   └── redis
├── traefik
│   ├── acme.json
│   ├── docker-compose.yml
│   ├── dockprom
│   └── traefik.toml
└── web
    ├── index.html
    ├── index.php
    └── nginx.conf

~~~

### Docker
So the Docker's containers are designed as below :

![docker](/content/images/docker.png)

## How to run it

### 1. docker-compose.yaml
There is just some little modifications to do to the _docker-compose.yaml_ :

~~~
version: '3.7'

services:
  proxy:
    image: traefik:latest #v1.6.6-alpine
    command: --web --docker --logLevel=INFO
    restart: unless-stopped
    networks:
      - ghost
      - backend-web
      - nextcloud
      - adminer-web
    ports:
      - "80:80"
      - "443:443"
    labels:
      # Step 1 : Make sure to change the host with your own IP or domain
      - "traefik.frontend.rule=Host:traefik.my-own-domain.com"
      - "traefik.port=8080"
    volumes:
      - /var/run/docker.sock:/var/run/docker.sock
      - ./traefik/traefik.toml:/traefik.toml
      - ./traefik/acme.json:/acme.json
  server:
    image: ghost:latest
    container_name: ghost
    restart: unless-stopped
    networks:
      - ghost
    labels:
      - "traefik.enable=true"
      # Step 2 : Make sure to change the host with your own IP or domain
      - "traefik.frontend.rule=Host:ghost.my-own-domain.com"
      - "traefik.port=2368"
      - "traefik.docker.network=ghost"
    volumes:
      - ./ghost/blog:/var/lib/ghost/content
    environment:
      - NODE_ENV=production
      # Step 3 : Make sure to change the host with your own IP or domain
      - url=https://ghost.my-own-domain.com
  web:
    image: nginx:latest
    container_name: web
    volumes:
        - ./web:/usr/share/nginx/html
        - ./web/nginx.conf:/etc/nginx/conf.d/default.conf
    labels:
        # Step 4 : Make sure to change the host with your own IP or domain
        - "traefik.frontend.rule=Host:web.my-own-domain.com"
        - "traefik.port=80"
        - "traefik.enable=true"
    networks:
      - backend-web
    depends_on:
      - php
  php:
    image: pichouk/php
    container_name: php
    volumes:
        - ./web:/usr/share/nginx/html
    networks:
      - backend-web
  postgresql:
    image: postgres:10
    container_name: postgresql
    # Step 5 : Choose here the parameters for you postgresql database (in Adminer, the host will be "postgresql").
    environment:
      POSTGRES_DB: prism
      POSTGRES_USER: snowden
      POSTGRES_PASSWORD: nsa
    volumes:
      - ./bdd/web:/var/lib/postgresql/data
    networks:
      - backend-web
      - adminer-web  
  db:
    image: postgres
    restart: always
    volumes:
      - ./nextcloud/db:/var/lib/postgresql/data
    # Step 6 : Choose here the env.file for you db database, for nextcloud (in Adminer, the host will be "db").
    env_file:
      - ./nextcloud/db.env
    networks:
      - nextcloud
      - internal

  app:
    image: nextcloud:latest
    restart: always
    volumes:
      - ./nextcloud/app:/var/www/html # Pulls from /var/lib/docker/volumes/nextcloud_nextcloud/_data/
      - ./nextcloud/config:/var/www/html/config # Pulls from local dir
      # Step 7 : Choose here the volume you want to mount on your nextcloud
      - /mnt/volume-nextcloud:/mnt/hdd # Pulls from root
    # Step 8 : Choose here the env.file for you db database, for nextcloud (in Adminer, the host will be "db").
    env_file:
      - ./nextcloud/db.env
    depends_on:
      - db
    networks:
      - nextcloud
      - internal
    labels:
      - "traefik.backend=nextcloud"
      - "traefik.docker.network=nextcloud"
      - "traefik.enable=true"
      # Step 9 : Make sure to change the host with your own IP or domain
      - "traefik.frontend.rule=Host:nextcloud.my-own-domain.com"
      - "traefik.port=80"

  redis:
    image: redis
    container_name: redis
    volumes:
      - ./nextcloud/redis:/data
    networks:
      - nextcloud
      - internal
  adminer:
    image: dockette/adminer:full-php5
    restart: always
    ports:
      - 8080:8080
    networks:
      - adminer-web
    labels:
      - "traefik.backend=adminer"
      - "traefik.docker.network=adminer-web"
      - "traefik.enable=true"
      # Step 10 : Make sure to change the host with your own IP or domain
      - "traefik.frontend.rule=Host:adminer.my-own-domain.com"
      - "traefik.port=80"
    depends_on:
      - postgresql
      - db
networks:
  internal:
  nextcloud:
    external: true
  ghost:
    external: true
  backend-web:
    external: true
  adminer-web:
    external: true
~~~

### 2. traefik.toml
Change also this in the _traefik/traefik.toml_ :

~~~
...
[docker]
endpoint = "unix:///var/run/docker.sock"
# Put here your main domain name
domain = "my-own-domain.com"
watch = true
exposedbydefault = false

[acme]
# Put here your mail adress in order to be informed by a bot when you will need to ask for a new SSL certificate
email = "your@adress.com"
...
~~~

### 3. Run 
Then you just need to run docker :
~~~
docker-compose up -d 
cd traefik
touch acme.json
chmod 600 acme.json
git clone https://github.com/stefanprodan/dockprom
cd dockprom
ADMIN_USER=admin ADMIN_PASSWORD=admin docker-compose up -d
~~~

## Enjoy !
