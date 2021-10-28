# Gumbo Millennium e-voting

Online voting. A bad idea but neccessary.

## Deployment

The code is deployed on Google Cloud Run with a Cloud SQL database driving the whole ordeal.

It also has a bunch of secrets that need content, these are:

1. Messagebird config, with keys
   1. `access_key`
   2. `origin`
2. Conscribo config, with keys
    1. `account`
    2. `username`
    3. `password`
3. MySQL config (auto provisioned)

Example configs are:

### Messagebird

```json
{
    "access_key": "key",
    "origin": "Gumbo"
}
```

### Conscribo

```json
{
    "account": "gumbo-millennium",
    "username": "google-cloud",
    "password": "my-strong-password!"
}
```
