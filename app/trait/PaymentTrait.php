<?php

namespace app\trait;

use Shopify\Auth\FileSessionStorage;
use Shopify\Auth\Session;
use Shopify\Context;

trait PaymentTrait
{
    public function setUp()
    {
        $path = runtime_path('/tmp/php_sessions');
        $scopes = [
            'unauthenticated_read_product_listings',
            'unauthenticated_read_product_tags',
            'unauthenticated_read_checkouts',
            'unauthenticated_write_checkouts'
        ];
        Context::initialize(
            apiKey: env('SHOPIFY_API_KEY'),
            apiSecretKey: env('SHOPIFY_API_SECRET'),
            scopes: $scopes,
            hostName: env('SHOPIFY_APP_HOST_NAME'),
            sessionStorage: new FileSessionStorage($path),
            apiVersion: env('SHOPIFY_API_VERSION'),
            isEmbeddedApp: true,
            isPrivateApp: false,
        );
    }

    public function getPaySession() : Session
    {
        $session = new Session(uniqid(time()), env('SHOPIFY_APP_HOST_NAME'), true, md5(time()));
        $session->setAccessToken(env('SHOPIFY_API_ADMIN_TOKEN'));
        return $session;
    }
}