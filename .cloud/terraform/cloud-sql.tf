# Create a MySQL sever
resource "google_sql_database_instance" "db_mysql" {
  name                = "${var.app_prefix}-mysql"
  database_version    = "MYSQL_8_0"
  deletion_protection = false

  settings {
    tier = var.cloud_sql_machine

    disk_autoresize     = false
    deletion_protection = false

    maintenance_window {
      day  = 7
      hour = 0
    }
  }
}

# Create a database in the MySQL server
resource "google_sql_database" "laravel" {
  instance = google_sql_database_instance.db_mysql.name
  name     = local.cloud_sql_database
}

# And create a user in our server
resource "google_sql_user" "users" {
  instance = google_sql_database_instance.db_mysql.name
  name     = local.cloud_sql_username
  password = local.cloud_sql_password
}

# Add a resource to bind Cloud Run properly
data "google_sql_database_instance" "db_mysql" {
  name = google_sql_database_instance.db_mysql.name
}
