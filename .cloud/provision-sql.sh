#!/usr/bin/env bash

ROOT_PASSWORD=$( pwgen 40 1 )

gcloud sql instances create \
    --tier=db-n1-standard-1 \
    --region=europe-west4 \
    --storage-size 5 \
    --root-password "$ROOT_PASSWORD" \
    evoting-prod

echo "Root password set to [$ROOT_PASSWORD]"

gcloud sql databases create \
    --instance=evoting-prod \
    --charset=utf8-mb4 \
    evoting_prod
