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

# MySQL database
resource "google_secret_manager_secret" "cloud_sql_database" {
  secret_id = "${var.app_prefix}-cloud-sql-database"

  replication {
    user_managed {
      replicas {
        location = var.region
      }
    }
  }
}

# MySQL username
resource "google_secret_manager_secret" "cloud_sql_username" {
  secret_id = "${var.app_prefix}-cloud-sql-username"

  replication {
    user_managed {
      replicas {
        location = var.region
      }
    }
  }
}

# MySQL password
resource "google_secret_manager_secret" "cloud_sql_password" {
  secret_id = "${var.app_prefix}-cloud-sql-password"

  replication {
    user_managed {
      replicas {
        location = var.region
      }
    }
  }
}

# Messagebird, access key
resource "google_secret_manager_secret" "messagebird_access_key" {
  secret_id = "${var.app_prefix}-messagebird-access-key"

  replication {
    user_managed {
      replicas {
        location = var.region
      }
    }
  }
}

# Messagebird, origin
resource "google_secret_manager_secret" "messagebird_origin" {
  secret_id = "${var.app_prefix}-messagebird-origin"

  replication {
    user_managed {
      replicas {
        location = var.region
      }
    }
  }
}

# Conscribo, account
resource "google_secret_manager_secret" "conscribo_account" {
  secret_id = "conscribo-account"

  replication {
    user_managed {
      replicas {
        location = var.region
      }
    }
  }
}

# Conscribo, username
resource "google_secret_manager_secret" "conscribo_username" {
  secret_id = "conscribo-username"

  replication {
    user_managed {
      replicas {
        location = var.region
      }
    }
  }
}

# Conscribo, password
resource "google_secret_manager_secret" "conscribo_password" {
  secret_id = "conscribo-password"

  replication {
    user_managed {
      replicas {
        location = var.region
      }
    }
  }
}
