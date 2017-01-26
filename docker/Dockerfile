FROM richarvey/nginx-php-fpm

RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer.phar && alias composer='/usr/local/bin/composer.phar'
RUN mkdir -p /opt/croissants && rm /etc/nginx/sites-enabled/default.conf
COPY croissants.conf /etc/nginx/sites-enabled/croissants.conf
COPY croissants.tar /opt/croissants
RUN cd /opt/croissants && tar -xvf /opt/croissants/croissants.tar
RUN chmod -R 777 /opt/croissants
ADD crontab /root/croissant-cron
RUN cat /root/croissant-cron >> /etc/crontabs/root