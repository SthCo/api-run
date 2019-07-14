# Architecture du projet API RUN P19
A sample dockerized multi-site.

![bckg](/content/images/bckg.png)

## Required
* Docker
* Git
* (Optional) several domain registred in a DNS
## Services
You can display 7 services :
* https://ghost.my-own-domain.com : Ghost blog (setup page : https://ghost.my-own-domain.com/ghost/#/setup/one).
* http://traefik.ghost.my-own-domain.com : Traefik dashboard.
* http://traefik.my-own-domain.com:3000 : Grafana dashboard. 
* https://web.my-own-domain.com : A simple PHP + PostgreSQL website
* https://nextcloud.my-own-domain.com : Nextcloud.
* https://adminer.my-own-domain.com : A Adminer dashboard which will allow you to manage the 3 databases of the project.
* https://portainer.my-own-domain.com : A Portainer dashboard.

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

### (Optional) Your own DNS Server with bind9

First install bind9
~~~
apt-get update
apt-get install bind9 bind9utils bind9-doc dnsutils 
~~~

Then 
~~~
sudo nano /etc/bind/db.your-own-domain.com
~~~

Fill the file like below :
~~~
$TTL    10800
@       IN      SOA     ns1.your-own-domain.com. root.your-own-domain.com. (
                    2019070901         ; Serial
                         10800         ; Refresh
                         86400         ; Retry
                       2419200         ; Expire
                        604800 )       ; Negative Cache TTL
 ;
@       IN      NS      ns1
ns1     IN      A       your-ip
ghost    IN      A       your-ip
web    IN      A       your-ip
nextcloud    IN      A       your-ip
traefik     IN      A       your-ip
adminer     IN      A       your-ip
portainer   IN      A       your-ip

~~~

And
~~~
sudo nano /etc/bind/named.conf.local
~~~

Again fill the file like below :
~~~
//
// Do any local configuration here
//

// Consider adding the 1918 zones here, if they are not used in your
// organization
//include "/etc/bind/zones.rfc1918";

 zone "your-own-domain.com" {
           type master;
           file "/etc/bind/db.your-own-domain.com";
      };


~~~

Finally run bind9
~~~
sudo systemctl start bind9
~~~
And check the status 
~~~
sudo systemctl status bind9
~~~

*You will also need to configure your domain name provider with the same parameters you put in _db.your-own-domain.com_.*




### 0. Install docker

~~~
sudo apt-get install apt-transport-https ca-certificates curl gnupg2 software-properties-common
curl -fsSL https://download.docker.com/linux/debian/gpg | sudo apt-key add -
~~~

Remplacer <version> par le résultat de :
   
~~~
lsb_release -cs
~~~
~~~
sudo add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/debian <version> -cs stable"
~~~
Puis 
~~~
curl -fsSL https://download.docker.com/linux/debian/gpg | sudo apt-key add -
sudo apt-get install docker-ce
~~~

### 1. docker-compose.yml
There is just some little modifications (10 Steps) to do to the _docker-compose.yml_ :

~~~
cd /opt
git clone https://github.com/SthCo/api-run.git
cd api-run
sudo nano docker-compose.yml
~~~

Récupérez ici https://github.com/docker/compose/releases la dernière version et remplacez '1.24.1' par cette dernière
~~~
sudo curl -L https://github.com/docker/compose/releases/download/1.24.1/docker-compose-`uname -s`-`uname -m` -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
~~~


~~~
version: '3.7'

services:
  proxy:
    image: traefik:latest
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

### 2. networks

Create networks
~~~
docker network create internal
docker network create nextcloud
docker network create ghost
docker network create backend-web
docker network create adminer-web
docker network create portainer
~~~
### 3. traefik.toml
Change also this in the _traefik/traefik.toml_ :

~~~
sudo nano traefik/traefik.toml
~~~

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
# Uncomment this if you want to test your domain against ACME staging environment  
# caServer = "https://acme-staging.api.letsencrypt.org/directory"
...
~~~

### 3. Run 
Then you just need to run docker :
~~~
touch traefik/acme.json
chmod 600 traefik/acme.json
docker-compose up -d 
cd traefik
git clone https://github.com/stefanprodan/dockprom
cd dockprom
ADMIN_USER=admin ADMIN_PASSWORD=admin docker-compose up -d
~~~


## Enjoy !
