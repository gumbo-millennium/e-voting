#!/usr/bin/env bash

# Get path of the .github folder
SCRIPT_PATH="$( cd "$(dirname "$0")" ; cd ..; pwd -P )"

# Find the secrets folder
SECRETS_PATH="$SCRIPT_PATH/secrets"

# Find existing secrets in gcloud
EXISTING_SECRETS=$(gcloud secrets list --format="value(name)")

# Create each file in the secrets as secret in gcloud, but only if it doesn't exist
for file in $SECRETS_PATH/*.json; do
    # Get the name of the file
    filename=$( basename "$file" )

    # Get the name of the secret
    secret_name="${filename%.*}"

    # Check if the secret already exists
    if [[ $EXISTING_SECRETS == *"$secret_name"* ]]; then
        echo "Secret $secret_name already exists"
    else
        echo "Creating secret $secret_name"
        gcloud secrets create "$secret_name" --replication-policy="automatic" --data-file="$file"
    fi
done
