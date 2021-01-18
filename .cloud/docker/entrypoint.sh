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

echo " ============== SELF-CHECK ============== "
if [ -z "$APP_KEY" ]; then
    echo "Application key NOT SET\!"
    echo "Creating one for now..."
    php /var/www/laravel/artisan key:generate
else
    echo "Application key SET"
fi

if [ "$GOOGLE_CLOUD" = "run" ]; then
    echo " ============== GOOGLE CONFIG ============== "

    # Use the PORT environment variable in Apache configuration files.
    # https://cloud.google.com/run/docs/reference/container-contract#port
    echo "Replacing port with requested port ${PORT}"
    sed -i -r "s/listen [0-9]+;/listen ${PORT};/g" \
        /etc/nginx/sites-available/*

    echo "Configuring SQL socket"
    export DB_HOST=""
    export DB_PORT=""
    export DB_SOCKET="${DB_SOCKET_DIR:-/cloudsql}/${CLOUD_SQL_CONNECTION_NAME}"
    export DATABASE_URL="mysql:dbname=${DB_DATABASE};unix_socket=${DB_SOCKET}"
fi


echo " ============== PRE-LAUNCH ============== "
echo "Migrating and optimizing application"
php /var/www/laravel/artisan migrate --force
php /var/www/laravel/artisan optimize

echo " ============== SUPPLY ============== "
echo "Pulling users if required"
php /var/www/laravel/artisan vote:prep-gcr

echo " ============== LAUNCHING ============== "
exec supervisord -c /etc/supervisor/supervisord.conf $@
