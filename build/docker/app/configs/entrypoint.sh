#!/usr/bin/env sh

chown -R www-data:www-data /var/www/
su -s /bin/sh www-data -c "cd /var/www && php bin/console doctrine:migrations:migrate --quiet --no-interaction --allow-no-migration"

/usr/bin/supervisord -n -c /etc/supervisord.conf