FROM ubuntu:xenial

ARG DATABASE

ADD http://getcomposer.org/download/1.1.2/composer.phar /usr/bin/
ADD http://github.com/etraxis/etraxis/archive/master.tar.gz /var/www/

COPY parameters.$DATABASE.yml /var/www/etraxis-master/app/config/parameters.yml
COPY start /usr/bin/start-etraxis

WORKDIR /var/www/etraxis-master

RUN apt-get update && \
    apt-get install -y unzip git npm php7.0-cli php7.0-json php7.0-xml php7.0-mbstring php7.0-bcmath php7.0-ldap php7.0-$DATABASE --no-install-recommends && \
    ln -s /usr/bin/nodejs /usr/bin/node && \
    npm install -g gulp bower && \
    chmod +x /usr/bin/composer.phar /usr/bin/start-etraxis && \
    tar -zxf /var/www/master.tar.gz -C /var/www && \
    composer.phar --no-interaction install && \
    ./bin/console --env=prod cache:warmup && \
    rm -r /var/lib/apt/lists/* /root/.cache /root/.composer /root/.npm /var/www/master.tar.gz

EXPOSE 8000

CMD [ "/usr/bin/start-etraxis" ]
