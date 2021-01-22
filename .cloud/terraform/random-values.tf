resource "random_id" "server_prefix" {
  keepers = {
    app_prefix = var.app_prefix
  }

  byte_length = 8
}

resource "random_id" "app_key_bytes" {
  keepers = {
    app_prefix = var.app_prefix
  }

  byte_length = 32
}
