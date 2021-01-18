resource "random_id" "server_prefix" {
  keepers = {
    app_prefix = var.app_prefix
  }

  byte_length = 8
}
