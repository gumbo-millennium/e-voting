locals {
  # Randoms
  server_prefix = random_id.server_prefix.hex

  # App key
  app_token = data.google_secret_manager_secret_version.app_token.secret_data

  # Cloud SQL
  cloud_sql_raw      = jsondecode(data.google_secret_manager_secret_version.cloud_sql.secret_data)
  cloud_sql_database = tostring(try(local.cloud_sql_raw.database, null))
  cloud_sql_username = tostring(try(local.cloud_sql_raw.username, null))
  cloud_sql_password = tostring(try(local.cloud_sql_raw.password, null))

  # Messagebird
  messagebird_raw        = jsondecode(data.google_secret_manager_secret_version.messagebird.secret_data)
  messagebird_access_key = tostring(try(local.messagebird_raw.access_key, null))
  messagebird_origin     = tostring(try(local.messagebird_raw.origin, null))

  # Conscribo API
  conscribo_raw      = jsondecode(data.google_secret_manager_secret_version.conscribo.secret_data)
  conscribo_account  = tostring(try(local.conscribo_raw.account, null))
  conscribo_username = tostring(try(local.conscribo_raw.username, null))
  conscribo_password = tostring(try(local.conscribo_raw.password, null))
}
