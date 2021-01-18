#!/usr/bin/env sh

is_set () {
    if test -z "$2"; then
        echo "$1: !! NOT SET !!"
    else
        echo "$1: SET OK"
    fi
}

echo " ============== Services ============== "
is_set "Google Cloud Project ID" $GOOGLE_CLOUD_PROJECT_ID
is_set "Google Cloud SQL connection" $CLOUD_SQL_CONNECTION_NAME
is_set "Google Cloud Storage bucket" $GOOGLE_CLOUD_STORAGE_BUCKET

echo " ============== SECRETS ============== "
is_set "Application key" $APP_KEY

is_set "Messagebird Key" $MESSAGEBIRD_ACCESS_KEY
is_set "Messagebird Origin" $MESSAGEBIRD_ORIGINATOR

is_set "Conscribo Site" $CONSCRIBO_ACCOUNT
is_set "Conscribo User" $CONSCRIBO_USERNAME
is_set "Conscribo Pass" $CONSCRIBO_PASSWORD

if [ "$GOOGLE_CLOUD" = "run" ]; then
    echo " ============== GOOGLE CONFIG ============== "

    # Use the PORT environment variable in Apache configuration files.
    # https://cloud.google.com/run/docs/reference/container-contract#port
    echo "Replacing port with requested port ${PORT}"
    sed -i -r "s/listen [0-9]+;/listen ${PORT};/g" \
        /etc/nginx/sites-available/*

    echo "Configuring Cloud SQL socket"
    export DB_SOCKET="${DB_SOCKET_DIR:-/cloudsql}/${CLOUD_SQL_CONNECTION_NAME}"
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
