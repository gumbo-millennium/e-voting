#!/usr/bin/env bash

# Get path of the .github folder
SCRIPT_PATH="$( cd "$(dirname "$0")" ; cd ..; pwd -P )"

# Find the secrets folder
SECRETS_PATH="$SCRIPT_PATH/secrets"

# Create each file in the secrets as secret in gcloud, but only if it doesn't exist
for file in $SECRETS_PATH/*.json; do
    # Get the name of the file
    filename=$( basename "$file" )

    # Get the name of the secret
    secret_name="${filename%.*}"

    # Delete the secret
    echo "Destroying secret $secret_name"
    gcloud secrets delete "$secret_name" --quiet
done
