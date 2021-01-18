variable "credentials_file" {
  type      = string
  sensitive = true
}

variable "app_prefix" {
  type    = string
  default = "evoting2021"

  validation {
    condition     = can(regex("^[a-z]", var.app_prefix))
    error_message = "App prefix must start with a letter."
  }
  validation {
    condition     = can(regex("^[a-z0-9-]+$", var.app_prefix))
    error_message = "App prefix must only use lowercase letters, numbers and hyphens."
  }
}

variable "project" {
  type    = string
  default = "stellar-shard-294215"
}

variable "region" {
  type    = string
  default = "europe-west4"
}

variable "zone" {
  type    = string
  default = "europe-west4-b"
}

variable "container_name" {
  type    = string
  default = "evoting"
}

variable "container_version" {
  type    = string
  default = "latest"
}

variable "container_region" {
  type    = string
  default = "eu"
}

variable "cloud_sql_machine" {
  type    = string
  default = "db-n1-standard-1"
}
