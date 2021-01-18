# App token
resource "google_secret_manager_secret" "app_token" {
  secret_id = "${var.app_prefix}-app-token"

  replication {
    user_managed {
      replicas {
        location = var.region
      }
    }
  }
}

# MySQL settings
resource "google_secret_manager_secret" "cloud_sql" {
  secret_id = "${var.app_prefix}-cloud-sql"

  replication {
    user_managed {
      replicas {
        location = var.region
      }
    }
  }
}

# Messagebird
resource "google_secret_manager_secret" "messagebird" {
  secret_id = "${var.app_prefix}-messagebird"

  replication {
    user_managed {
      replicas {
        location = var.region
      }
    }
  }
}

# Conscribo
resource "google_secret_manager_secret" "conscribo" {
  secret_id = "conscribo"

  replication {
    user_managed {
      replicas {
        location = var.region
      }
    }
  }
}
