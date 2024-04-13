<?php

return [
    'api_key' => env("SHOPIFY_API_KEY",'68360e67c85f9298c197fd0c6cd6ebbd'),
    'api_secret'=>env('SHOPIFY_API_SECRET','e4793109a820938ee7c77311f40bfd26'),
    'app_scopes'=>env('SHOPIFY_APP_SCOPES',''),
    'app_host'=>env('SHOPIFY_APP_HOST_NAME','cashbusol.myshopify.com'),
    'api_admin_token'=>env('SHOPIFY_API_ADMIN_TOKEN','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'),
    'api_store_token'=>env('SHOPIFY_API_STOREFRONT_TOKEN','8560491febb509d95688f55ee1de6140'),
    'app_version'=>env('SHOPIFY_API_VERSION','2023-04'),
    'customer_password'=>env('SHOPIFY_CUSTOMER_DEFAULT_PASSWORD','ddhd@2024'),
];