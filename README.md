# Instagram oauth

A script to ease the authentication flow for Instagram.

## How to use

If you want a access token you must:

1. Have a link to the script with the query parameter `site` that contains your endpoint that will receive the token.  
Ex: `https://example.com?site=http://mysite.com/instagram`
2. This will forward the user to the regular Instagram login page.
3. The user will be redirected to the page specified in `?site` with the query variable `access_token`.

## Whitelist

Only domains that are on the whitelist will be able to fetch a access_token.

The whitelist is located in the config.yml file, and supports wildcards:

```
whitelist:
  - http://*.example.com/* # Allows all subdomains and path of example.com to request tokens.
  - https://example.com/specific/endpoint # Without wildcards the path must match exact.
```
