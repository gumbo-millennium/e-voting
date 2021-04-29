# Messagebird Settings
resource "google_secret_manager_secret" "messagebird" {
  secret_id = "messagebird"

  replication {
    automatic = "true"
  }
}

# Conscribo Settings
resource "google_secret_manager_secret" "conscribo" {
  secret_id = "conscribo"

  replication {
    automatic = "true"
  }
}
