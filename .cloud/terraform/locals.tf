locals {
  # App key
  app_token = data.google_secret_manager_secret_version.app_token.secret_data

  # Cloud SQL
  cloud_sql_database = data.google_secret_manager_secret_version.cloud_sql_database.secret_data
  cloud_sql_username = data.google_secret_manager_secret_version.cloud_sql_username.secret_data
  cloud_sql_password = data.google_secret_manager_secret_version.cloud_sql_password.secret_data

  # Messagebird
  messagebird_access_key = data.google_secret_manager_secret_version.messagebird_access_key.secret_data
  messagebird_origin     = data.google_secret_manager_secret_version.messagebird_origin.secret_data

  # Conscribo API
  conscribo_account  = data.google_secret_manager_secret_version.conscribo_account.secret_data
  conscribo_username = data.google_secret_manager_secret_version.conscribo_username.secret_data
  conscribo_password = data.google_secret_manager_secret_version.conscribo_password.secret_data
}
