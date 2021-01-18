# Now create our Google Cloud Run service
resource "google_cloud_run_service" "default" {
  name     = "${local.server_prefix}-laravel-app"
  location = var.region

  template {
    spec {
      containers {
        image = data.google_container_registry_image.application.image_url
        # Cloud SQL
        env {
          name  = "CLOUD_SQL_CONNECTION_NAME"
          value = data.google_sql_database_instance.db_mysql.connection_name
        }

        # Dynamic
        env {
          name  = "GOOGLE_CLOUD_PROJECT_ID"
          value = google_storage_bucket.site_object_cache.project
        }
        env {
          name  = "GOOGLE_CLOUD_STORAGE_BUCKET"
          value = google_storage_bucket.site_object_cache.name
        }

        # App key
        env {
          name  = "APP_KEY"
          value = local.app_token
        }

        # Database secrets
        env {
          name  = "DB_DATABASE"
          value = local.cloud_sql_database
        }
        env {
          name  = "DB_USERNAME"
          value = local.cloud_sql_username
        }
        env {
          name  = "DB_PASSWORD"
          value = local.cloud_sql_password
        }

        # Messagebird secrets
        env {
          name  = "MESSAGEBIRD_ACCESS_KEY"
          value = local.messagebird_access_key
        }
        env {
          name  = "MESSAGEBIRD_ORIGINATOR"
          value = local.messagebird_origin
        }

        # Concribo secrets
        env {
          name  = "CONSCRIBO_ACCOUNT"
          value = local.conscribo_account
        }
        env {
          name  = "CONSCRIBO_USERNAME"
          value = local.conscribo_username
        }
        env {
          name  = "CONSCRIBO_PASSWORD"
          value = local.conscribo_password
        }
      }
    }

    # Define the data, such as Cloud SQL connection
    metadata {
      annotations = {
        "autoscaling.knative.dev/maxScale"      = "1000"
        "run.googleapis.com/cloudsql-instances" = data.google_sql_database_instance.db_mysql.connection_name
        "run.googleapis.com/client-name"        = "terraform"
      }
    }
  }

  # Always route all traffic to most-recent version
  traffic {
    percent         = 100
    latest_revision = true
  }

  # Don't care about the revision name
  autogenerate_revision_name = true
}

# Create a policy to allow anyone to access
data "google_iam_policy" "noauth" {
  binding {
    role = "roles/run.invoker"
    members = [
      "allUsers",
    ]
  }
}
# And asssign the IAM policy to our Cloud Run Service
resource "google_cloud_run_service_iam_policy" "noauth" {
  location = google_cloud_run_service.default.location
  project  = google_cloud_run_service.default.project
  service  = google_cloud_run_service.default.name

  policy_data = data.google_iam_policy.noauth.policy_data
}

# Also add our domain mapping
resource "google_cloud_run_domain_mapping" "default" {
  location = var.region
  name     = "e-voting.gumbo-millennium.nl"

  metadata {
    namespace = var.project
  }

  spec {
    route_name = google_cloud_run_service.default.name
  }
}
