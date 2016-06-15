### Get docker

Refer to [www.docker.com](https://www.docker.com/) for installation details.

### eTraxis on MySQL

```bash
docker build -t etraxis:mysql --build-arg DATABASE=mysql .
docker run -d --name mysql -e MYSQL_ALLOW_EMPTY_PASSWORD=yes mysql:5.5
docker run -d --name etraxis --link mysql:mysql -p 8000:8000 etraxis:mysql
```

### eTraxis on PostgreSQL

```bash
docker build -t etraxis:pgsql --build-arg DATABASE=pgsql .
docker run -d --name pgsql postgres:9.1
docker run -d --name etraxis --link pgsql:pgsql -p 8000:8000 etraxis:pgsql
```

### Ready to go

Visit [localhost:8000](http://localhost:8000/).
