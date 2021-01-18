# App token
data "google_secret_manager_secret_version" "app_token" {
  secret = google_secret_manager_secret.app_token.name
}

# MySQL database
data "google_secret_manager_secret_version" "cloud_sql_database" {
  secret = google_secret_manager_secret.cloud_sql_database.name
}

# MySQL username
data "google_secret_manager_secret_version" "cloud_sql_username" {
  secret = google_secret_manager_secret.cloud_sql_username.name
}

# MySQL password
data "google_secret_manager_secret_version" "cloud_sql_password" {
  secret = google_secret_manager_secret.cloud_sql_password.name
}

# Messagebird, access key
data "google_secret_manager_secret_version" "messagebird_access_key" {
  secret = google_secret_manager_secret.messagebird_access_key.name
}

# Messagebird, origin
data "google_secret_manager_secret_version" "messagebird_origin" {
  secret = google_secret_manager_secret.messagebird_origin.name
}

# Conscribo, account
data "google_secret_manager_secret_version" "conscribo_account" {
  secret = google_secret_manager_secret.conscribo_account.name
}

# Conscribo, username
data "google_secret_manager_secret_version" "conscribo_username" {
  secret = google_secret_manager_secret.conscribo_username.name
}

# Conscribo, password
data "google_secret_manager_secret_version" "conscribo_password" {
  secret = google_secret_manager_secret.conscribo_password.name
}
