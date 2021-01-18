terraform {
  backend "remote" {
    organization = "gumbo-millennium"

    workspaces {
      name = "e-voting"
    }
  }

  required_providers {
    google = {
      source  = "hashicorp/google"
      version = "3.52.0"
    }
  }
}

provider "google" {
  credentials = file(var.credentials_file)

  project = var.project
  region  = var.region
  zone    = var.zone
}
