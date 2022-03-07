# Gumbo Millennium e-voting

Online voting. A bad idea but neccessary.

## Read-only update

As of February 25th, 2022, we've stopped using this application. 

Although it might be a bit easier to use than paper voting with large crowds, it's less explainable, less open and just can't be verified as easily as paper allots. The code should still work without too many issues, but we've decided to go the known, old route of just giving everyone a piece of paper.

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
