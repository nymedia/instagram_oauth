# Instagram oauth

A script to ease the authentication flow for Instagram.

## How to use

If you want a access token you must:

1. Have a link to `https://instagram.nymedia.no` with the query parameter `site` that contains your endpoint that will receive the token.  
Ex: `https://instagram.nymedia.no?site=http://boardshop.no/oauth/instagram`
2. This will forward the user to the regular Instagram login page.
3. The user will be redirected to the page specified in `?site` with the query variable `access_token`.

## Other stuff

Script is located at dev2, in `/home/support/instagram_oauth`.
