#!/usr/bin/env sh

is_set () {
    if test -z "$2"; then
        echo "$1: !! NOT SET !!"
    else
        echo "$1: SET OK"
    fi
}

echo " ============== DRIVERS ============== "
echo "Cache driver: ${CACHE_DRIVER:-(UNSET)}"
echo "Queue driver: ${QUEUE_CONNECTION:-(UNSET)}"
echo "Session driver: ${SESSION_DRIVER:-(UNSET)}"
echo "Log channel: ${LOG_CHANNEL:-(UNSET)}"

echo " ============== SECRETS ============== "
is_set "Messagebird Key" $MESSAGEBIRD_ACCESS_KEY
is_set "Messagebird Origin" $MESSAGEBIRD_ORIGINATOR

is_set "Conscribo Site" $CONSCRIBO_ACCOUNT
is_set "Conscribo User" $CONSCRIBO_USERNAME
is_set "Conscribo Pass" $CONSCRIBO_PASSWORD

echo " ============== PRE-LAUNCH ============== "
echo "Migrating and optimizing application"
php /var/www/laravel/artisan migrate --force
php /var/www/laravel/artisan optimize

echo " ============== SUPPLY ============== "
echo "Pulling users if required"
php /var/www/laravel/artisan vote:prep-gcr

echo " ============== LAUNCHING ============== "
exec supervisord -c /etc/supervisor/supervisord.conf $@
