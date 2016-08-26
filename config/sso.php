<?php

return [

    'sso_token_encrypt_key'=>env('SSO_TOKEN_ENCRYPT_KEY','test'),

    'sso_login_url'=>env('SSO_LOGIN_URL','http://localhost:8000/api/merchant/sso/login'),

    'sso_logout_url'=>env('SSO_LOGOUT_URL','http://localhost:3000/users/login?logout=1')

];
