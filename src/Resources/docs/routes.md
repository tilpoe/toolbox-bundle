## Fallback

**default path:** / `ALL`

This route renders the standard twig file that
is configured via the *twig_fallback_path* config variable.

## Auth

**default path:** /api/auth `POST`

This route handles the authentication of the webpage.

#### Parameters

---

**`grant_type`** string

Sepecifies the action the authenticator executes.

Can be one of: *password*, *refresh_token*, *logout*

###### password

Tries to log in the user with `username` and `password`. 

---

`username` string (null, if `grant_type` is not *password*)

---

`password` string (null, if `grant_type` is not *password*)

