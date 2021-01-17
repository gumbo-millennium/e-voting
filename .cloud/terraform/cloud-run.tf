# Now create our Google Cloud Run service
resource "google_cloud_run_service" "default" {
  name     = "${var.app_prefix}-laravel-app"
  location = var.region

  template {
    spec {
      containers {
        image = data.google_container_registry_image.application.image_url
        env {
          name  = "APP_STORAGE"
          value = "/tmp"
        }
        env {
          name  = "VIEW_COMPILED_PATH"
          value = "/tmp"
        }
        env {
          name  = "CACHE_DRIVER"
          value = "database"
        }
        env {
          name  = "SESSION_DRIVER"
          value = "database"
        }
        env {
          name  = "LOG_CHANNEL"
          value = "stackdriver"
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

        # Secret
        env {
          name  = "DB_DATABASE"
          value = var.cloud_sql_database
        }
        env {
          name  = "DB_USERNAME"
          value = var.cloud_sql_username
        }
        env {
          name  = "DB_PASSWORD"
          value = var.cloud_sql_password
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
