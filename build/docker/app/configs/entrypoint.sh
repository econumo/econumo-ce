#!/usr/bin/env sh

ECONUMO_CONFIG_API_URL="${ECONUMO_CONFIG_API_URL:=}"
LILTAG_CONFIG_URL="${LILTAG_CONFIG_URL:=}"
LILTAG_CACHE_TTL="${LILTAG_CACHE_TTL:=0}"
echo "window.econumoConfig = {
  API_URL: \"${ECONUMO_CONFIG_API_URL}\",
  LILTAG_CONFIG_URL: \"${LILTAG_CONFIG_URL}\",
  LILTAG_CACHE_TTL: ${LILTAG_CACHE_TTL},
}" > /usr/share/nginx/html/econumo-config.js

chown -R www-data:www-data /var/www/ /usr/share/nginx/html/
su -s /bin/sh www-data -c "cd /var/www && php bin/console doctrine:migrations:migrate --quiet --no-interaction --allow-no-migration"

/usr/bin/supervisord -n -c /etc/supervisord.conf
