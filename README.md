# Architecture du projet API RUN P19
A sample of dockerized multi-site (configured with bind9 DNS).

![bckg](/content/images/bckg.svg)


You can display 6 services :
* https://monsite1.lucas.picasoft.net : Ghost blog.
* http://traefik.lucas.picasoft.net : Traefik dashboard.
* http://traefik.lucas.picasoft.net:3000/d/Vv-WpYSZz/docker-containers?orgId=1&refresh=5s : Grafana dashboard. 
* https://monsite2.lucas.picasoft.net : A simple PHP + PostgreSQL website
* https://monsite3.lucas.picasoft.net : Nextcloud.
* https://adminer.lucas.picasoft.net : A Adminer dashboard which will allow you to manage the 3 databases of the project.

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

So the Docker's containers are designed as below :

![docker](/content/images/docker.png)

There is just some little modifications to do to the docker-compose.yaml :

~~~

~~~


Then you just need to run docker :
~~~
docker-compose up -d 
cd traefik/dockprom
ADMIN_USER=admin ADMIN_PASSWORD=admin docker-compose up -d
~~~
