# Messagebird Settings
data "google_secret_manager_secret_version" "messagebird" {
  secret = google_secret_manager_secret.messagebird.name
}

# Conscribo Settings
data "google_secret_manager_secret_version" "conscribo" {
  secret = google_secret_manager_secret.conscribo.name
}
