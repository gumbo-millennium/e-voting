locals {
  # Randoms
  server_prefix = random_id.server_prefix.hex

  # App key
  app_token = "base64:${random_id.app_key_bytes.b64_std}"

  # Cloud SQL
  cloud_sql = {
    database = "${local.server_prefix}-mysql"
    username = "${local.server_prefix}-mysql-user"
    password = random_password.mysql_password.result
  }

  # Messagebird
  messagebird_raw = jsondecode(data.google_secret_manager_secret_version.messagebird.secret_data)
  messagebird = {
    access_key = tostring(try(local.messagebird_raw.access_key, null))
    origin     = tostring(try(local.messagebird_raw.origin, null))
  }

  # Conscribo API
  conscribo_raw = jsondecode(data.google_secret_manager_secret_version.conscribo.secret_data)
  conscribo = {
    account  = tostring(try(local.conscribo_raw.account, null))
    username = tostring(try(local.conscribo_raw.username, null))
    password = tostring(try(local.conscribo_raw.password, null))
  }
}
