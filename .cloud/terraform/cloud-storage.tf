resource "google_storage_bucket" "site_object_cache" {
  name          = "${local.server_prefix}-app-storage"
  location      = var.region
  force_destroy = true

  uniform_bucket_level_access = true

  # Allow cross-origin requests
  cors {
    origin = ["e-voting.gumbo-millennium.nl"]
    method = ["GET"]
  }

  # Purge files over 14 days old
  lifecycle_rule {
    condition {
      age = 14
    }
    action {
      type = "Delete"
    }
  }
}
