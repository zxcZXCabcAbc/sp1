<?php

namespace app\service\shopify\action\admin;

use app\service\shopify\ShopifyApiService;

class Location extends ShopifyApiService
{

    /**
     * @param $since_id
     * @return array|string|void|null
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @doc https://shopify.dev/docs/api/admin-rest/2024-01/resources/country
     *
     */
    public function getCountry($since_id = 0)
    {
        try {
            $path = sprintf('/admin/api/%s/countries.json', self::$api_version);
            $query = $since_id ? ['since_id'=>$since_id] : [];
            $response = $this->api->get($path, [],$query);
            return $response->getDecodedBody();
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function getProvices($countryId)
    {
        try{
            $path = sprintf('/admin/api/%s/countries/%s/provinces.json',self::$api_version,$countryId);
            $response = $this->api->get($path);
            return $response->getDecodedBody();
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }
}