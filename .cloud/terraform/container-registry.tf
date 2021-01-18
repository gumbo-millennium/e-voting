data "google_container_registry_image" "application" {
  name    = var.container_name
  project = var.project
  region  = var.container_region
  tag     = var.container_version
}

