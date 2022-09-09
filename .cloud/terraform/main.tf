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
      version = "4.35.0"
    }

    random = {
      source  = "hashicorp/random"
      version = "3.4.3"
    }
  }
}

provider "google" {
  credentials = file(var.credentials_file)

  project = var.project
  region  = var.region
  zone    = var.zone
}
